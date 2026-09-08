/**
 * Encrypt HTML locally and upload (or replace) only the ciphertext on html.cloud.
 *
 * The crypto and the encrypt-then-upload sequence are imported from the
 * published `html-cloud` package so the MCP server, the CLI, the browser
 * extension and the web client all share one implementation — the
 * zero-knowledge model must never fork across them.
 */

import {
  shareDocument, updateDocument, parseEditLink, MAX_SIZE,
} from 'html-cloud/share-core.js';

export { MAX_SIZE };

const EXPIRES = ['7', '30', 'never'];

function resolveBaseUrl(baseUrl) {
  return (baseUrl ?? process.env.HTML_CLOUD_URL ?? 'https://html.cloud').replace(/\/+$/, '');
}

function encodeHtml(html) {
  const plaintext = new TextEncoder().encode(html);
  if (plaintext.length === 0) throw new Error('html is empty');
  if (plaintext.length > MAX_SIZE) throw new Error('html is too large (max 10 MB)');
  return plaintext;
}

/** fetch throws a TypeError when the host is unreachable — say so plainly. */
async function reaching(baseUrl, work) {
  try {
    return await work();
  } catch (err) {
    if (err instanceof TypeError) throw new Error(`could not reach ${baseUrl}`);
    throw err;
  }
}

function links(baseUrl, { id, viewFrag, editFrag }) {
  return {
    id,
    shareUrl: `${baseUrl}/v/${id}#${viewFrag}`,
    editUrl:  `${baseUrl}/e/${id}#${editFrag}`,
  };
}

/**
 * @param {string} html        The HTML content to share.
 * @param {object} [opts]
 * @param {'7'|'30'|'never'} [opts.expires='30']
 * @param {string} [opts.baseUrl]  Server base URL (default html.cloud / $HTML_CLOUD_URL).
 * @returns {Promise<{id:string, shareUrl:string, editUrl:string, expires:string}>}
 */
export async function shareHtml(html, opts = {}) {
  const expires = opts.expires ?? '30';
  if (!EXPIRES.includes(expires)) {
    throw new Error(`expires must be 7, 30 or never (got "${expires}")`);
  }
  const baseUrl   = resolveBaseUrl(opts.baseUrl);
  const plaintext = encodeHtml(html);

  const result = await reaching(baseUrl, () =>
    shareDocument(plaintext, { expiresIn: expires, baseUrl }));

  return { ...links(baseUrl, result), expires };
}

/**
 * Replace the content behind an existing share. The edit link (returned by
 * shareHtml) proves ownership; the share link stays the same because the new
 * HTML is encrypted under the document's existing view key.
 *
 * @param {string} editLink  The private https://html.cloud/e/{id}#{key} link.
 * @param {string} html      The new, full HTML document.
 * @returns {Promise<{id:string, shareUrl:string, editUrl:string}>}
 */
export async function updateHtml(editLink, html) {
  const { baseUrl, id, editFrag } = parseEditLink(editLink);
  const plaintext = encodeHtml(html);

  const result = await reaching(baseUrl, () =>
    updateDocument(id, editFrag, plaintext, { baseUrl }));

  return links(baseUrl, result);
}
