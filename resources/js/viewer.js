import { importViewKey, decryptBytes, unpackCiphertext, b64urlDecode } from './crypto.js';

const docId       = window.__DOC_ID__;
const loadScreen  = document.getElementById('loading-screen');
const errorScreen = document.getElementById('error-screen');
const errorTitle  = document.getElementById('error-title');
const errorBody   = document.getElementById('error-body');
const frame       = document.getElementById('content-frame');

function showError(title, body) {
  errorTitle.textContent = title;
  errorBody.textContent  = body;
  loadScreen.classList.add('hidden');
  errorScreen.classList.remove('hidden');
}

async function main() {
  const fragment = window.location.hash.slice(1);
  if (!fragment) {
    return showError('Missing decryption key', 'The link is incomplete — the part after # is the key and must not be removed.');
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
</style>
<div id="__hc__">
  <svg width="11" height="11" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="7" width="10" height="8" rx="1.5"/><path d="M5 7V5a3 3 0 0 1 6 0v2"/></svg>Encrypted &middot; shared via <a href="https://html.cloud" target="_blank" rel="noopener">html.cloud</a>
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
}

main();
