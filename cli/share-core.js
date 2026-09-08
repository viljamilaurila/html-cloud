/**
 * The one place that defines what "uploading a document" means.
 *
 * Browser (homepage), CLI (`npx html-cloud`) and the Chrome extension all share
 * this single encrypt-then-upload sequence so the zero-knowledge wire contract
 * can never silently diverge between clients — exactly like crypto.js.
 *
 * Lives in cli/ (next to crypto.js) so the npm package can ship it; the browser
 * re-exports it via resources/js/share-core.js.
 */

import {
  generateViewKey, generateEditKey, exportViewKey, importViewKey,
  encryptBytes, encryptViewKeyWithEditKey, decryptViewKeyWithEditKey,
  computeEditAuth, packCiphertext, b64url, b64urlDecode,
} from './crypto.js';

export const MAX_SIZE = 10 * 1024 * 1024; // 10 MB — also enforced server-side

/**
 * Cosmetic, URL-safe slug from a filename. It rides in the share link purely so
 * previews show a title — never stored, never used to look up the document.
 */
export function slugify(name) {
  return name
    .replace(/\.html?$/i, '')
    .normalize('NFKD').replace(/[̀-ͯ]/g, '') // strip accents: ä -> a
    .toLowerCase()
    .replace(/[^a-z0-9]+/g, '-')
    .replace(/^-+|-+$/g, '')
    .slice(0, 60)
    .replace(/-+$/, '');
}

/** Build the viewer path. A slug, when present, makes the link self-describing. */
export function viewPath(id, slug) {
  return slug ? `/v/${id}/${slug}` : `/v/${id}`;
}

/**
 * Split an edit link (`https://host/e/{id}#{editKey}`) into the pieces a
 * client needs to talk to the server about that document. The key stays in
 * the fragment, exactly where the link carries it — it is never sent anywhere
 * except as the `edit_key` proof inside an authorized write.
 *
 * @param {string} link
 * @returns {{ baseUrl: string, id: string, editFrag: string }}
 */
export function parseEditLink(link) {
  let url;
  try {
    url = new URL(String(link).trim());
  } catch {
    throw new Error('Not a valid edit link.');
  }
  const match    = url.pathname.match(/^\/e\/([A-Za-z0-9]+)\/?$/);
  const editFrag = url.hash.slice(1);
  if (!match || !editFrag) {
    throw new Error('Not an edit link — expected the private https://html.cloud/e/{id}#{key} link.');
  }
  return { baseUrl: url.origin, id: match[1], editFrag };
}

/**
 * Encrypt plaintext bytes into the server wire payload plus the two URL-fragment
 * keys. Pure crypto, no network — keys are generated here and never leave except
 * inside the returned fragment strings.
 *
 * @param {Uint8Array} plaintext
 * @returns {{ payload: object, viewFrag: string, editFrag: string }}
 */
export async function encryptDocument(plaintext) {
  const viewKey    = await generateViewKey();
  const editKeyRaw = await generateEditKey();
  const viewKeyRaw = await exportViewKey(viewKey);

  const { iv, ciphertext } = await encryptBytes(viewKey, plaintext);

  return {
    payload: {
      ciphertext:         packCiphertext(iv, ciphertext),
      encrypted_view_key: await encryptViewKeyWithEditKey(viewKeyRaw, editKeyRaw),
      edit_auth:          await computeEditAuth(editKeyRaw),
      size:               plaintext.length,
    },
    viewFrag: b64url(viewKeyRaw),
    editFrag: b64url(editKeyRaw),
  };
}

/**
 * Encrypt + upload a document. Returns the server id and the two fragment keys.
 * The caller owns everything after this: building links, copying, remembering.
 *
 * @param {Uint8Array} plaintext
 * @param {object}   [opts]
 * @param {string}   [opts.expiresIn='30']  '7' | '30' | 'never'
 * @param {boolean}  [opts.sensitive]       omitted from the body when undefined
 * @param {string}   [opts.baseUrl='']      '' = same-origin; absolute for CLI/extension
 * @param {object}   [opts.headers={}]      extra request headers (extension, CLI)
 * @param {Function} [opts.fetchImpl=fetch] injectable fetch (background worker / Node)
 * @returns {Promise<{ id: string, viewFrag: string, editFrag: string }>}
 */
export async function shareDocument(plaintext, {
  expiresIn = '30',
  sensitive,
  baseUrl = '',
  headers = {},
  fetchImpl = fetch,
} = {}) {
  const { payload, viewFrag, editFrag } = await encryptDocument(plaintext);

  const body = { ...payload, expires_in: expiresIn };
  if (sensitive !== undefined) body.sensitive = sensitive;

  const res = await fetchImpl(`${baseUrl}/api/documents`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', ...headers },
    body: JSON.stringify(body),
  });

  if (!res.ok) {
    if (res.status === 429) {
      throw new Error('Too many uploads — please wait a few minutes and try again.');
    }
    const err = await res.json().catch(() => ({}));
    throw new Error(err.error || err.message || `Upload failed (HTTP ${res.status})`);
  }

  const { id } = await res.json();
  return { id, viewFrag, editFrag };
}

/**
 * Replace the content of an existing document. The new plaintext is encrypted
 * under the document's *existing* view key (unwrapped locally with the edit
 * key), so every share link already handed out keeps working unchanged. Only
 * the edit-key holder can do this; the server still sees only ciphertext.
 *
 * @param {string}     id         Document id from the edit link.
 * @param {string}     editFrag   base64url edit key from after the # in the edit link.
 * @param {Uint8Array} plaintext
 * @param {object}     [opts]                  Same transport options as shareDocument.
 * @param {string}     [opts.baseUrl='']
 * @param {object}     [opts.headers={}]
 * @param {Function}   [opts.fetchImpl=fetch]
 * @returns {Promise<{ id: string, viewFrag: string, editFrag: string }>}
 */
export async function updateDocument(id, editFrag, plaintext, {
  baseUrl = '',
  headers = {},
  fetchImpl = fetch,
} = {}) {
  let editKeyRaw;
  try {
    editKeyRaw = b64urlDecode(editFrag);
  } catch {
    throw new Error('Invalid edit key.');
  }

  const current = await fetchImpl(`${baseUrl}/api/documents/${id}`, { headers });
  if (current.status === 404) {
    throw new Error('Document not found — it may have expired or been deleted.');
  }
  if (!current.ok) {
    throw new Error(`Could not load document (HTTP ${current.status})`);
  }
  const { encrypted_view_key: encryptedViewKey } = await current.json();

  let viewKeyRaw;
  try {
    viewKeyRaw = await decryptViewKeyWithEditKey(encryptedViewKey, editKeyRaw);
  } catch {
    throw new Error('Invalid edit key for this document.');
  }

  const viewKey = await importViewKey(viewKeyRaw);
  const { iv, ciphertext } = await encryptBytes(viewKey, plaintext);

  const res = await fetchImpl(`${baseUrl}/api/documents/${id}`, {
    method: 'PUT',
    headers: { 'Content-Type': 'application/json', ...headers },
    body: JSON.stringify({
      ciphertext:         packCiphertext(iv, ciphertext),
      encrypted_view_key: await encryptViewKeyWithEditKey(viewKeyRaw, editKeyRaw),
      edit_key:           b64url(editKeyRaw),
      size:               plaintext.length,
    }),
  });

  if (!res.ok) {
    if (res.status === 403) throw new Error('Invalid edit key for this document.');
    const err = await res.json().catch(() => ({}));
    throw new Error(err.error || err.message || `Update failed (HTTP ${res.status})`);
  }

  return { id, viewFrag: b64url(viewKeyRaw), editFrag: b64url(editKeyRaw) };
}
