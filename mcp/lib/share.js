/**
 * Encrypt an HTML string locally and upload only the ciphertext to html.cloud.
 *
 * The crypto module is imported from the published `html-cloud` package so the
 * MCP server, the CLI, and the web client all share one implementation — the
 * zero-knowledge model must never fork across them.
 */

import {
  generateViewKey, generateEditKey, exportViewKey,
  encryptBytes, encryptViewKeyWithEditKey, computeEditAuth,
  packCiphertext, b64url,
} from 'html-cloud/crypto.js';

export const MAX_SIZE = 10 * 1024 * 1024; // 10 MB, matches the server limit

/**
 * @param {string} html        The HTML content to share.
 * @param {object} [opts]
 * @param {'7'|'30'|'never'} [opts.expires='30']
 * @param {string} [opts.baseUrl]  Server base URL (default html.cloud / $HTML_CLOUD_URL).
 * @returns {Promise<{id:string, shareUrl:string, editUrl:string, expires:string}>}
 */
export async function shareHtml(html, opts = {}) {
  const expires = opts.expires ?? '30';
  if (!['7', '30', 'never'].includes(expires)) {
    throw new Error(`expires must be 7, 30 or never (got "${expires}")`);
  }
  const baseUrl = (opts.baseUrl ?? process.env.HTML_CLOUD_URL ?? 'https://html.cloud')
    .replace(/\/+$/, '');

  const plaintext = new TextEncoder().encode(html);
  if (plaintext.length === 0) throw new Error('html is empty');
  if (plaintext.length > MAX_SIZE) throw new Error('html is too large (max 10 MB)');

  // Keys are generated here and never sent to the server.
  const viewKey    = await generateViewKey();
  const editKeyRaw = await generateEditKey();
  const viewKeyRaw = await exportViewKey(viewKey);

  const { iv, ciphertext } = await encryptBytes(viewKey, plaintext);
  const packed             = packCiphertext(iv, ciphertext);
  const encryptedViewKey   = await encryptViewKeyWithEditKey(viewKeyRaw, editKeyRaw);
  const editAuth           = await computeEditAuth(editKeyRaw);

  let res;
  try {
    res = await fetch(`${baseUrl}/api/documents`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        ciphertext:         packed,
        encrypted_view_key: encryptedViewKey,
        edit_auth:          editAuth,
        expires_in:         expires,
        size:               plaintext.length,
      }),
    });
  } catch {
    throw new Error(`could not reach ${baseUrl}`);
  }

  if (!res.ok) {
    if (res.status === 429) throw new Error('too many uploads — please wait a few minutes and try again');
    const err = await res.json().catch(() => ({}));
    throw new Error(err.error || err.message || `upload failed (HTTP ${res.status})`);
  }

  const { id } = await res.json();
  return {
    id,
    shareUrl: `${baseUrl}/v/${id}#${b64url(viewKeyRaw)}`,
    editUrl:  `${baseUrl}/e/${id}#${b64url(editKeyRaw)}`,
    expires,
  };
}
