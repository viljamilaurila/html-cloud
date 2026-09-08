import { test } from 'node:test';
import assert from 'node:assert/strict';
import { FRAME_SHIM, instrumentDocument } from '../../resources/js/frame-shim.js';

test('shim is a single well-formed script tag', () => {
  assert.ok(FRAME_SHIM.startsWith('<script>'));
  assert.ok(FRAME_SHIM.endsWith('</script>'));
  // Only the closing tag may contain "</script>", or it would end the shim early.
  assert.equal(FRAME_SHIM.indexOf('</script>'), FRAME_SHIM.length - '</script>'.length);
  assert.ok(FRAME_SHIM.includes('__hcbg'), 'keeps the background reporter');
  assert.ok(FRAME_SHIM.includes('location.hash'), 'handles in-page anchors');
});

test('injects right after <head>, leaving the rest byte-identical', () => {
  const html = '<!doctype html><html><HEAD lang="en"><title>x</title></HEAD><body><a href="#b">b</a></body></html>';
  const out = instrumentDocument(html);
  assert.equal(out, '<!doctype html><html><HEAD lang="en">' + FRAME_SHIM + '<title>x</title></HEAD><body><a href="#b">b</a></body></html>');
});

test('only the first <head> is instrumented', () => {
  const html = '<head></head><head></head>';
  assert.equal(instrumentDocument(html), '<head>' + FRAME_SHIM + '</head><head></head>');
});

test('prepends when the document has no <head>', () => {
  const html = '<h1>bare</h1>';
  assert.equal(instrumentDocument(html), FRAME_SHIM + html);
});

test('does not mutate its input', () => {
  const html = '<head></head>';
  instrumentDocument(html);
  assert.equal(html, '<head></head>');
});
