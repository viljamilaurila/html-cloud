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
  generateViewKey, generateEditKey, exportViewKey,
  encryptBytes, encryptViewKeyWithEditKey, computeEditAuth,
  packCiphertext, b64url,
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
