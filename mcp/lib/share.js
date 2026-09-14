/**
 * Encrypt HTML locally and upload (or replace) only the ciphertext on html.cloud.
 *
 * The crypto and the encrypt-then-upload sequence are imported from the
 * published `html-cloud` package so the MCP server, the CLI, the browser
 * extension and the web client all share one implementation — the
 * zero-knowledge model must never fork across them.
 */

import { readFileSync } from 'node:fs';
import { basename } from 'node:path';

import {
  shareDocument, updateDocument, parseEditLink, MAX_SIZE,
} from 'html-cloud/share-core.js';

export { MAX_SIZE };

const EXPIRES = ['7', '30', 'never'];

function resolveBaseUrl(baseUrl) {
  return (baseUrl ?? process.env.HTML_CLOUD_URL ?? 'https://html.cloud').replace(/\/+$/, '');
}

function encodeHtml(html) {
  const plaintext = html instanceof Uint8Array ? html : new TextEncoder().encode(html);
  if (plaintext.length === 0) throw new Error('html is empty');
  if (plaintext.length > MAX_SIZE) throw new Error('html is too large (max 10 MB)');
  return plaintext;
}

/**
 * Resolve a tool call's input to the bytes to encrypt. The assistant passes
 * either the HTML inline (`html`) or the path of an .html file it wrote
 * (`path`) — the file route exists because a large page (hundreds of KB) can
 * exceed what an MCP host lets through as a tool argument. Only .html/.htm
 * files are accepted: this server shares web pages, and it must not become a
 * general way to upload arbitrary files from the user's computer.
 *
 * @param {{html?: string, path?: string}} input
 * @returns {Uint8Array}
 */
export function loadHtml({ html, path } = {}) {
  const hasHtml = typeof html === 'string' && html.length > 0;
  const hasPath = typeof path === 'string' && path.trim().length > 0;
  if (hasHtml && hasPath) throw new Error('pass either html or path, not both');
  if (hasHtml) return encodeHtml(html);
  if (!hasPath) throw new Error('pass the HTML as html, or the path of an .html file as path');

  const file = path.trim();
  if (!/\.html?$/i.test(file)) {
    throw new Error(`path must point to an .html or .htm file (got "${basename(file)}")`);
  }
  let bytes;
  try {
    bytes = new Uint8Array(readFileSync(file));
  } catch {
    throw new Error(`cannot read ${file}`);
  }
  if (bytes.length === 0) throw new Error(`${file} is empty`);
  return encodeHtml(bytes);
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
 * @param {string|Uint8Array} html  The HTML content to share (see loadHtml).
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
 * @param {string} editLink            The private https://html.cloud/e/{id}#{key} link.
 * @param {string|Uint8Array} html     The new, full HTML document (see loadHtml).
 * @returns {Promise<{id:string, shareUrl:string, editUrl:string}>}
 */
export async function updateHtml(editLink, html) {
  const { baseUrl, id, editFrag } = parseEditLink(editLink);
  const plaintext = encodeHtml(html);

  const result = await reaching(baseUrl, () =>
    updateDocument(id, editFrag, plaintext, { baseUrl }));

  return links(baseUrl, result);
}
