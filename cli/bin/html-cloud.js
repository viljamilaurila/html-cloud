#!/usr/bin/env node
/**
 * npx html-cloud ./file.html
 *
 * Encrypts an HTML file locally (AES-256-GCM) and uploads only the
 * ciphertext to html.cloud. Keys are generated here and printed as URL
 * fragments — they are never sent to the server.
 */

import { spawnSync } from 'node:child_process';
import { readFileSync } from 'node:fs';
import { basename } from 'node:path';
import { parseArgs } from 'node:util';

import { shareDocument, updateDocument, parseEditLink, MAX_SIZE } from '../share-core.js';

const HELP = `
html-cloud — private HTML file sharing, encrypted before upload

Usage:
  npx html-cloud <file.html> [options]
  cat page.html | npx html-cloud -
  npx html-cloud <file.html> --update <edit-link>

Options:
  --expires <7|30|never>  Days until the link expires (default: 30)
  --update <edit-link>    Replace the content behind an existing share. Pass
                          the private edit link a previous run printed; the
                          share link stays the same
  --url <base>            Server base URL (default: https://html.cloud,
                          or $HTML_CLOUD_URL)
  --no-copy               Don't copy the share link to the clipboard
  -h, --help              Show this help

The file is encrypted with AES-256-GCM in this process. The decryption
key is placed after the # in the link — browsers never send that part
to servers, and html.cloud stores only ciphertext.
`.trim();

function fail(msg) {
  console.error(`error: ${msg}`);
  process.exit(1);
}

/** Copy text via the OS clipboard command. Returns true on success. */
function copyToClipboard(text) {
  const candidates = process.platform === 'darwin' ? [['pbcopy']]
    : process.platform === 'win32' ? [['clip']]
    : [['wl-copy'], ['xclip', '-selection', 'clipboard'], ['xsel', '--clipboard', '--input']];
  for (const [cmd, ...cmdArgs] of candidates) {
    const r = spawnSync(cmd, cmdArgs, { input: text, stdio: ['pipe', 'ignore', 'ignore'] });
    if (!r.error && r.status === 0) return true;
  }
  return false;
}

let args;
try {
  args = parseArgs({
    allowPositionals: true,
    options: {
      expires:   { type: 'string' },
      update:    { type: 'string' },
      url:       { type: 'string' },
      'no-copy': { type: 'boolean', default: false },
      help:      { type: 'boolean', short: 'h', default: false },
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

// --update replaces an existing document: the edit link names the server and
// the document, and expiry is left untouched (change it from the edit page).
let editLink = null;
if (args.values.update !== undefined) {
  try {
    editLink = parseEditLink(args.values.update);
  } catch (err) {
    fail(err.message.toLowerCase().replace(/\.$/, ''));
  }
  if (args.values.expires !== undefined) {
    fail('--expires cannot be combined with --update (expiry is unchanged by an update)');
  }
}

const expires = args.values.expires ?? '30';
if (!['7', '30', 'never'].includes(expires)) {
  fail(`--expires must be 7, 30 or never (got "${expires}")`);
}
const baseUrl = (args.values.url ?? editLink?.baseUrl ?? process.env.HTML_CLOUD_URL ?? 'https://html.cloud')
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

// Encrypt locally and upload ciphertext only — shared with the website and the
// browser extension via share-core.js. Keys are generated inside and returned
// as URL fragments; they never leave this process except in the printed links.
// An update re-uses the document's existing view key (unwrapped locally with
// the edit key) so the share link that is already out there keeps working.
let id, viewFrag, editFrag;
try {
  ({ id, viewFrag, editFrag } = editLink
    ? await updateDocument(editLink.id, editLink.editFrag, plaintext, { baseUrl })
    : await shareDocument(plaintext, { expiresIn: expires, baseUrl }));
} catch (err) {
  // fetch throws a TypeError when the host is unreachable; anything else is an
  // Error we raised with a ready-to-print message (server error, rate limit…).
  if (err instanceof TypeError) fail(`could not reach ${baseUrl}`);
  fail(err.message.toLowerCase());
}

const shareLink = `${baseUrl}/v/${id}#${viewFrag}`;

// Copy only in interactive use — never alter the clipboard from scripts/pipes.
const copied = !args.values['no-copy'] && process.stdout.isTTY && copyToClipboard(shareLink);

const expiryNote = editLink ? 'content replaced, expiry unchanged'
  : expires === 'never' ? 'never expires' : `expires in ${expires} days`;
console.log(`
Share link (${editLink ? 'unchanged — ' : ''}anyone with this can view)${copied ? ' — copied to clipboard' : ''}:
  ${shareLink}

Edit link (keep private — update, change expiry, delete):
  ${baseUrl}/e/${id}#${editFrag}

Encrypted locally with AES-256-GCM · ${expiryNote} · the server never saw the keys
`.trim());
