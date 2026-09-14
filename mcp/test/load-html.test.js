import { test } from 'node:test';
import assert from 'node:assert/strict';
import { mkdtempSync, writeFileSync } from 'node:fs';
import { tmpdir } from 'node:os';
import { join } from 'node:path';

import { loadHtml } from '../lib/share.js';

const dir = mkdtempSync(join(tmpdir(), 'html-cloud-mcp-'));
const decode = (bytes) => new TextDecoder().decode(bytes);

test('inline html is encoded as UTF-8 bytes', () => {
  assert.equal(decode(loadHtml({ html: '<h1>hei ä</h1>' })), '<h1>hei ä</h1>');
});

test('path reads the file instead of the html argument', () => {
  const file = join(dir, 'page.html');
  writeFileSync(file, '<p>from disk</p>');
  assert.equal(decode(loadHtml({ path: file })), '<p>from disk</p>');
});

test('.htm is accepted too, and surrounding whitespace in the path is ignored', () => {
  const file = join(dir, 'page.htm');
  writeFileSync(file, '<p>htm</p>');
  assert.equal(decode(loadHtml({ path: ` ${file} ` })), '<p>htm</p>');
});

test('neither argument is an error', () => {
  assert.throws(() => loadHtml({}), /pass the HTML as html, or the path/);
  assert.throws(() => loadHtml({ html: '', path: '' }), /pass the HTML as html, or the path/);
});

test('both arguments is an error', () => {
  assert.throws(() => loadHtml({ html: '<p>x</p>', path: join(dir, 'page.html') }), /not both/);
});

test('only .html/.htm files can be shared from disk', () => {
  const file = join(dir, 'secrets.txt');
  writeFileSync(file, 'hunter2');
  assert.throws(() => loadHtml({ path: file }), /must point to an \.html or \.htm file \(got "secrets\.txt"\)/);
});

test('a missing file is reported by path', () => {
  const file = join(dir, 'missing.html');
  assert.throws(() => loadHtml({ path: file }), new RegExp(`cannot read ${file}`));
});

test('an empty file is rejected', () => {
  const file = join(dir, 'empty.html');
  writeFileSync(file, '');
  assert.throws(() => loadHtml({ path: file }), /empty\.html is empty/);
});
