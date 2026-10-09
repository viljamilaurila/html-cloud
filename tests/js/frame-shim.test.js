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

/**
 * Run the shim against stubbed frame globals and return its click listener
 * and drag listeners, the URLs it opened in a new tab and what it posted to
 * the parent.
 */
function loadClickHandler() {
  const listeners = {};
  const opened = [];
  const posted = [];
  const source = FRAME_SHIM.slice('<script>'.length, -'</script>'.length);
  new Function('addEventListener', 'document', 'parent', 'location', 'open', 'scrollTo', 'getComputedStyle', source)(
    (type, fn) => { listeners[type] = fn; },
    { readyState: 'loading', body: {}, getElementById: () => null, getElementsByName: () => [] },
    { postMessage(message) { posted.push(message); } },
    { hash: '' },
    (...args) => { opened.push(args); },
    () => {},
    () => ({ backgroundColor: '' }),
  );
  return { click: listeners.click, dragenter: listeners.dragenter, opened, posted };
}

function clickOn(click, attrs, eventProps = {}) {
  const url = new URL(attrs.href, 'https://html.cloud/v/abc');
  const a = {
    href: url.href,
    protocol: url.protocol,
    getAttribute: (name) => attrs[name] ?? null,
    hasAttribute: (name) => name in attrs,
  };
  const event = {
    button: 0,
    defaultPrevented: false,
    target: { closest: () => a },
    preventDefault() { this.defaultPrevented = true; },
    ...eventProps,
  };
  click(event);
  return event;
}

test('external links open in a new tab instead of navigating the frame', () => {
  const { click, opened } = loadClickHandler();
  for (const target of [undefined, '_self', '_top', '_parent']) {
    const attrs = target ? { href: 'https://example.com/a', target } : { href: 'https://example.com/a' };
    assert.equal(clickOn(click, attrs).defaultPrevented, true);
  }
  assert.deepEqual(opened, Array(4).fill(['https://example.com/a', '_blank', 'noopener']));
});

test('links the browser already handles safely are left alone', () => {
  const { click, opened } = loadClickHandler();
  assert.equal(clickOn(click, { href: 'https://example.com/', target: '_blank' }).defaultPrevented, false);
  assert.equal(clickOn(click, { href: 'mailto:a@example.com' }).defaultPrevented, false);
  assert.equal(clickOn(click, { href: 'https://example.com/f.pdf', download: '' }).defaultPrevented, false);
  assert.equal(clickOn(click, { href: 'https://example.com/' }, { metaKey: true }).defaultPrevented, false);
  assert.deepEqual(opened, []);
});

test('dragging an HTML file over the document tells the parent, and nothing else', () => {
  const { dragenter, posted } = loadClickHandler();
  const drag = (...items) => {
    const event = { dataTransfer: { items }, defaultPrevented: false, preventDefault() { this.defaultPrevented = true; } };
    dragenter(event);
    return event;
  };

  const event = drag({ kind: 'file', type: 'text/html' });
  assert.deepEqual(posted.filter((m) => m.__hcdrag), [{ __hcdrag: 1 }]);
  assert.equal(event.defaultPrevented, false, 'the document can still handle the drop itself');

  posted.length = 0;
  drag({ kind: 'file', type: 'image/png' }, { kind: 'string', type: 'text/html' });
  assert.equal(posted.filter((m) => m.__hcdrag).length, 0, 'other files and dragged text are ignored');
});
