#!/usr/bin/env node
/**
 * npx html-cloud ./file.html
 *
 * Encrypts an HTML file locally (AES-256-GCM) and uploads only the
 * ciphertext to html.cloud. Keys are generated here and printed as URL
 * fragments — they are never sent to the server.
 */

import { readFileSync } from 'node:fs';
import { basename } from 'node:path';
import { parseArgs } from 'node:util';

import {
  generateViewKey, generateEditKey, exportViewKey,
  encryptBytes, encryptViewKeyWithEditKey, computeEditAuth,
  packCiphertext, b64url,
} from '../crypto.js';

const MAX_SIZE = 10 * 1024 * 1024; // 10 MB, matches the server limit

const HELP = `
html-cloud — private HTML file sharing, encrypted before upload

Usage:
  npx html-cloud <file.html> [options]
  cat page.html | npx html-cloud -

Options:
  --expires <7|30|never>  Days until the link expires (default: 30)
  --url <base>            Server base URL (default: https://html.cloud,
                          or $HTML_CLOUD_URL)
  -h, --help              Show this help

The file is encrypted with AES-256-GCM in this process. The decryption
key is placed after the # in the link — browsers never send that part
to servers, and html.cloud stores only ciphertext.
`.trim();

function fail(msg) {
  console.error(`error: ${msg}`);
  process.exit(1);
}

let args;
try {
  args = parseArgs({
    allowPositionals: true,
    options: {
      expires: { type: 'string', default: '30' },
      url:     { type: 'string' },
      help:    { type: 'boolean', short: 'h', default: false },
    },
  });
} catch (err) {
  fail(err.message);
}

if (args.values.help || args.positionals.length === 0) {
  console.log(HELP);
  process.exit(args.values.help ? 0 : 1);
}

const input = args.positionals[0];
const expires = args.values.expires;
if (!['7', '30', 'never'].includes(expires)) {
  fail(`--expires must be 7, 30 or never (got "${expires}")`);
}
const baseUrl = (args.values.url ?? process.env.HTML_CLOUD_URL ?? 'https://html.cloud')
  .replace(/\/+$/, '');

let plaintext;
if (input === '-') {
  plaintext = new Uint8Array(readFileSync(0));
} else {
  if (!/\.html?$/i.test(input)) fail(`expected an .html or .htm file (got "${basename(input)}")`);
  try {
    plaintext = new Uint8Array(readFileSync(input));
  } catch {
    fail(`cannot read ${input}`);
  }
}
if (plaintext.length === 0) fail('input is empty');
if (plaintext.length > MAX_SIZE) fail('file is too large (max 10 MB)');

// 1. Generate keys locally — these never leave this process except inside the printed links.
const viewKey    = await generateViewKey();
const editKeyRaw = await generateEditKey();
const viewKeyRaw = await exportViewKey(viewKey);

// 2. Encrypt content and wrap the view key with the edit key.
const { iv, ciphertext } = await encryptBytes(viewKey, plaintext);
const packed             = packCiphertext(iv, ciphertext);
const encryptedViewKey   = await encryptViewKeyWithEditKey(viewKeyRaw, editKeyRaw);
const editAuth           = await computeEditAuth(editKeyRaw);

// 3. Upload ciphertext only.
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
  fail(`could not reach ${baseUrl}`);
}

if (!res.ok) {
  if (res.status === 429) fail('too many uploads — please wait a few minutes and try again');
  const err = await res.json().catch(() => ({}));
  fail(err.error || err.message || `upload failed (HTTP ${res.status})`);
}

const { id } = await res.json();

const expiryNote = expires === 'never' ? 'never expires' : `expires in ${expires} days`;
console.log(`
Share link (anyone with this can view):
  ${baseUrl}/v/${id}#${b64url(viewKeyRaw)}

Edit link (keep private — replace, change expiry, delete):
  ${baseUrl}/e/${id}#${b64url(editKeyRaw)}

Encrypted locally with AES-256-GCM · ${expiryNote} · the server never saw the keys
`.trim());
