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

  // Inject a subtle attribution footer after the document's own content.
  // The <style> block handles flex/grid body layouts so the badge always
  // appears as its own full-width row at the end, never floating beside content.
  const badge = `
<style>
  body { position: relative !important; padding-bottom: 40px !important; }
  #__hc__ {
    position: absolute !important;
    bottom: 0 !important;
    left: 0 !important;
    right: 0 !important;
    padding: 12px 24px !important;
    border-top: 1px solid rgba(0,0,0,0.07) !important;
    text-align: center !important;
    font-family: system-ui, -apple-system, sans-serif !important;
    font-size: 12px !important;
    color: #999 !important;
    line-height: 1.5 !important;
    box-sizing: border-box !important;
  }
  #__hc__ a { color: #3a4f3a !important; text-decoration: none !important; font-weight: 500 !important; }
  #__hc__ svg { display: inline !important; vertical-align: -2px !important; margin-right: 5px !important; opacity: .55 !important; }
  #__hc__ .copy { color: #3a4f3a !important; font-weight: 500 !important; cursor: pointer !important; }
  #__hc__ .copy:hover { text-decoration: underline !important; }
</style>
<div id="__hc__">
  <svg width="11" height="11" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="7" width="10" height="8" rx="1.5"/><path d="M5 7V5a3 3 0 0 1 6 0v2"/></svg>Encrypted &middot; shared via <a href="https://html.cloud" target="_blank" rel="noopener">html.cloud</a> &middot; <span class="copy" role="button" tabindex="0" onclick="parent.postMessage('hc:copy-link','*');var s=this,o=s.textContent;s.textContent='Link copied';setTimeout(function(){s.textContent=o},1800)">Copy link</span>
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
