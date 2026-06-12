import { importViewKey, decryptBytes, unpackCiphertext, b64url, b64urlDecode } from './crypto.js';

const docId       = window.__DOC_ID__;
const loadScreen  = document.getElementById('loading-screen');
const errorScreen = document.getElementById('error-screen');
const errorTitle  = document.getElementById('error-title');
const errorBody   = document.getElementById('error-body');
const frame       = document.getElementById('content-frame');

// Per-tab cache so the page survives a reload after we strip the key from the URL.
const SS_KEY = `hc_vk_${docId}`;

function showError(title, body) {
  errorTitle.textContent = title;
  errorBody.textContent  = body;
  loadScreen.classList.add('hidden');
  errorScreen.classList.remove('hidden');
}

// Read the key from the URL fragment if present, then strip it (and any query,
// such as the ?owner marker) from the address bar immediately — so the key can't
// leak via screen-sharing, history, or Referer.
// On a reload — where the fragment is already gone — fall back to the per-tab cache.
function takeKeyFragment() {
  const fromHash = window.location.hash.slice(1);
  if (fromHash) {
    history.replaceState(null, '', window.location.pathname);
    return { fragment: fromHash, fromHash: true };
  }
  let stored = '';
  try { stored = sessionStorage.getItem(SS_KEY) || ''; } catch { /* sessionStorage unavailable */ }
  return { fragment: stored, fromHash: false };
}

// Shown when there's no key at all — the most common confusion, since the key
// lives after the # and gets dropped when people copy from the address bar.
// Renders the visitor's own (incomplete) URL next to a complete one so the
// missing piece is obvious.
function showMissingKeyError() {
  errorTitle.textContent = 'This link is missing its key';
  errorBody.textContent =
    'Every working link ends with a key after the #. A complete one looks like this:';

  document.getElementById('mk-url-full').textContent = `${location.host}/v/${docId}`;

  document.getElementById('missing-key-help').classList.remove('hidden');
  loadScreen.classList.add('hidden');
  errorScreen.classList.remove('hidden');
}

async function main() {
  // The editor's "Open" button tags its link with ?owner so we can remind the
  // creator (the person most likely to re-share from the address bar) to copy
  // the link properly. Read it before takeKeyFragment() strips the query.
  const isOwner = new URLSearchParams(location.search).has('owner');

  const { fragment, fromHash } = takeKeyFragment();
  if (!fragment) {
    return showMissingKeyError();
  }

  let viewKeyRaw;
  try {
    viewKeyRaw = b64urlDecode(fragment);
  } catch {
    return showError('Invalid key', 'The key in the URL could not be decoded.');
  }

  let doc;
  try {
    const res = await fetch(`/api/documents/${docId}`);
    if (res.status === 404) return showError('File not found', 'This file may have expired or been removed by its owner.');
    if (!res.ok) throw new Error('Server error');
    doc = await res.json();
  } catch (err) {
    if (err.message !== 'Server error') return showError('File not found', 'This file may have expired or been removed by its owner.');
    return showError('Could not load file', 'A network error occurred. Please try again.');
  }

  let plaintext;
  try {
    const viewKey = await importViewKey(viewKeyRaw);
    const { iv, ciphertext } = unpackCiphertext(doc.ciphertext);
    plaintext = await decryptBytes(viewKey, iv, ciphertext);
  } catch {
    return showError('Wrong key', 'The key in this link doesn\'t match the file. Make sure you\'re using the full, unmodified share link.');
  }

  const html = new TextDecoder().decode(plaintext);

  // A small floating lock pill in the bottom-right corner — "found if you seek,"
  // never distracting. It's `position: fixed`, so it sits in its own layer and
  // never reflows the host document (the old full-width footer mutated the body's
  // layout, which collided with documents that had their own fixed bottom UI).
  // At rest it's just a quiet lock glyph; on hover/focus/tap it expands to reveal
  // the attribution and the Copy link control.
  const badge = `
<style>
  #__hc__ {
    position: fixed !important;
    right: 16px !important;
    bottom: 16px !important;
    z-index: 2147483647 !important;
    font-family: system-ui, -apple-system, sans-serif !important;
    font-size: 12.5px !important;
    line-height: 1.4 !important;
  }
  #__hc__ .hc-pill {
    display: inline-flex !important;
    align-items: center !important;
    background: #fbf9f4 !important;
    border: 0.5px solid rgba(58,79,58,0.22) !important;
    border-radius: 999px !important;
    padding: 7px !important;
    opacity: .7 !important;
    box-shadow: 0 1px 3px rgba(31,28,23,0.10) !important;
    cursor: pointer !important;
    transition: opacity .2s ease !important;
  }
  #__hc__ .hc-pill:hover, #__hc__ .hc-pill:focus-within, #__hc__ .hc-pill.hc-open { opacity: 1 !important; }
  #__hc__ .hc-lock { display: inline-flex !important; align-items: center !important; color: #3a4f3a !important; }
  #__hc__ .hc-lock svg { display: block !important; }
  #__hc__ .hc-body {
    display: inline-flex !important;
    align-items: center !important;
    gap: 10px !important;
    max-width: 0 !important;
    opacity: 0 !important;
    overflow: hidden !important;
    white-space: nowrap !important;
    transition: max-width .25s ease, opacity .2s ease, margin .25s ease !important;
  }
  #__hc__ .hc-pill:hover .hc-body, #__hc__ .hc-pill:focus-within .hc-body, #__hc__ .hc-pill.hc-open .hc-body {
    max-width: 340px !important;
    opacity: 1 !important;
    margin: 0 4px 0 10px !important;
  }
  #__hc__ .hc-txt { color: #5a5247 !important; }
  #__hc__ a, #__hc__ .copy { color: #3a4f3a !important; text-decoration: none !important; font-weight: 500 !important; }
  #__hc__ .copy { cursor: pointer !important; display: inline-flex !important; align-items: center !important; gap: 4px !important; }
  #__hc__ .copy:hover { text-decoration: underline !important; }
  #__hc__ .hc-sep { width: 1px !important; height: 14px !important; background: rgba(31,28,23,0.14) !important; }
</style>
<div id="__hc__">
  <div class="hc-pill" tabindex="0">
    <span class="hc-lock" role="button" tabindex="0" aria-label="Encrypted with html.cloud" onclick="if(!this.closest('.hc-pill').classList.toggle('hc-open'))this.blur()"><svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="7" width="10" height="8" rx="1.5"/><path d="M5 7V5a3 3 0 0 1 6 0v2"/></svg></span>
    <span class="hc-body">
      <span class="hc-txt">Encrypted &middot; <a href="https://html.cloud" target="_blank" rel="noopener">html.cloud</a></span>
      <span class="hc-sep"></span>
      <span class="copy" role="button" tabindex="0" onclick="event.stopPropagation();parent.postMessage('hc:copy-link','*');var s=this.querySelector('.copy-label'),o=s.textContent;s.textContent='Link copied';setTimeout(function(){s.textContent=o},1800)"><svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="5" width="9" height="9" rx="1.5"/><path d="M11 5V3.5A1.5 1.5 0 0 0 9.5 2H3.5A1.5 1.5 0 0 0 2 3.5v6A1.5 1.5 0 0 0 3.5 11H5"/></svg><span class="copy-label">Copy link</span></span>
    </span>
  </div>
  <script>
    (function () {
      var hc = document.getElementById('__hc__');
      // On touch there's no hover-out, so dismiss the expanded pill when the
      // visitor taps anywhere outside it (and clear focus so :focus-within
      // doesn't hold it open).
      document.addEventListener('click', function (e) {
        if (!hc.contains(e.target)) {
          var pill = hc.querySelector('.hc-pill');
          pill.classList.remove('hc-open');
          if (document.activeElement && hc.contains(document.activeElement)) document.activeElement.blur();
        }
      });
    })();
  </script>
</div>`;

  // Append before </body> if present, otherwise at the end.
  const injected = /<\/body>/i.test(html)
    ? html.replace(/<\/body>/i, badge + '</body>')
    : html + badge;

  // srcdoc works in sandboxed iframes without allow-same-origin.
  // blob: URLs require same-origin and go silently blank in our sandbox.
  frame.srcdoc = injected;
  frame.classList.remove('hidden');
  loadScreen.classList.add('hidden');

  // The key decrypted cleanly — remember it for this tab so a reload still works
  // even though the address bar no longer carries it.
  if (fromHash) {
    try { sessionStorage.setItem(SS_KEY, fragment); } catch { /* ignore */ }
  }

  // The address bar is now key-free. The "Copy link" control lives inside the
  // sandboxed iframe (the footer badge), but the key is kept out of that frame —
  // so the badge asks us (the parent, which holds the key) to copy the full link.
  const shareUrl = `${window.location.origin}/v/${docId}#${b64url(viewKeyRaw)}`;
  window.addEventListener('message', (e) => {
    if (e.source === frame.contentWindow && e.data === 'hc:copy-link') {
      copyToClipboard(shareUrl);
    }
  });

  // Creator previewing their own file: remind them to share via Copy link,
  // since the address bar no longer carries the key.
  if (isOwner) {
    const notice = document.getElementById('owner-notice');
    const copyBtn = document.getElementById('owner-copy');
    copyBtn.addEventListener('click', () => {
      copyToClipboard(shareUrl);
      copyBtn.textContent = 'Copied';
      setTimeout(() => { copyBtn.textContent = 'Copy link'; }, 1800);
    });
    document.getElementById('owner-dismiss').addEventListener('click', () => notice.remove());
    notice.classList.remove('hidden');
  }
}

async function copyToClipboard(text) {
  try { await navigator.clipboard.writeText(text); }
  catch {
    const ta = Object.assign(document.createElement('textarea'), { value: text, style: 'position:fixed;opacity:0' });
    document.body.appendChild(ta); ta.select(); document.execCommand('copy'); ta.remove();
  }
}

main();
