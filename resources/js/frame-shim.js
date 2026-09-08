/**
 * Instrumentation the viewer injects into a decrypted document before it is
 * rendered in the sandboxed srcdoc frame. Two tiny, self-contained scripts:
 *
 * 1. Background reporter (read-only). The frame is sandboxed/cross-origin, so
 *    the parent can't read its styles — instead the document reports its own
 *    computed body background via postMessage so the viewer can paint the
 *    letterbox area to match. Everything else renders as authored.
 *
 * 2. In-page anchor fix. A srcdoc document has no URL of its own, so a link
 *    like `href="#section"` resolves against the *parent* viewer URL and the
 *    browser treats the click as a navigation to a different document: the
 *    frame reloads the viewer with `#section` in place of the key and the
 *    content disappears. Same-document hash changes work fine inside srcdoc,
 *    so the shim turns such clicks into `location.hash` updates (keeping
 *    `:target`, `hashchange` and history intact) and scrolls to the target
 *    itself, since the sandboxed frame doesn't scroll to fragments on its own.
 *    Clicks the page already handled (`defaultPrevented`) and modifier-clicks
 *    are left alone; no other URL resolution is touched.
 *
 * Kept as plain strings so the bundler never rewrites what runs inside the
 * frame, with `</script>` split so the tag can't terminate our own script.
 */

const bgReporter =
  '(function(){function s(){try{parent.postMessage({__hcbg:getComputedStyle(document.body).backgroundColor},"*")}catch(e){}}' +
  'if(document.readyState!=="loading")s();else addEventListener("DOMContentLoaded",s);addEventListener("load",s)})();';

const anchorShim =
  '(function(){' +
  'function t(h){var id;try{id=decodeURIComponent(h.slice(1))}catch(e){id=h.slice(1)}' +
  'return id?(document.getElementById(id)||document.getElementsByName(id)[0]||null):null}' +
  'function g(){var h=location.hash,el=t(h);if(el)el.scrollIntoView();else if(h===""||h==="#"||h==="#top")scrollTo(0,0)}' +
  'addEventListener("click",function(e){' +
  'if(e.defaultPrevented||e.button!==0||e.metaKey||e.ctrlKey||e.shiftKey||e.altKey)return;' +
  'var a=e.target&&e.target.closest?e.target.closest("a[href]"):null;if(!a)return;' +
  'var h=a.getAttribute("href");if(!h||h.charAt(0)!=="#")return;' +
  'e.preventDefault();if(location.hash!==h&&!(h==="#"&&location.hash===""))location.hash=h;else g()});' +
  'addEventListener("hashchange",g)})();';

export const FRAME_SHIM = '<scr' + 'ipt>' + bgReporter + anchorShim + '<\/scr' + 'ipt>';

/**
 * Return the document HTML with the shim inserted at the top of <head>, or
 * prepended when the document has no <head>. The input is never mutated; the
 * caller must keep using the original bytes for Download.
 *
 * @param {string} html
 * @returns {string}
 */
export function instrumentDocument(html) {
  return /<head[^>]*>/i.test(html)
    ? html.replace(/<head[^>]*>/i, (m) => m + FRAME_SHIM)
    : FRAME_SHIM + html;
}
