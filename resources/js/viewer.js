import { importViewKey, decryptBytes, unpackCiphertext, b64url, b64urlDecode } from './crypto.js';
import { getUpload } from './uploads-store.js';

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

// Read the key from the URL fragment if present. We do NOT strip it here — that
// depends on the document's "sensitive" flag, which we only learn after fetching.
// Shareable (default): the key stays in the address bar so it's a working share
// link. Sensitive: stripKeyFromAddressBar() removes it once we know.
// On a reload of a stripped (sensitive) doc the fragment is gone, so fall back to
// the per-tab cache.
function readKeyFragment() {
  const fromHash = window.location.hash.slice(1);
  if (fromHash) return { fragment: fromHash, fromHash: true };
  let stored = '';
  try { stored = sessionStorage.getItem(SS_KEY) || ''; } catch { /* sessionStorage unavailable */ }
  return { fragment: stored, fromHash: false };
}

// For sensitive docs: cache the key for this tab (so a reload still works) and
// remove it from the address bar, so it can't leak via screen-sharing or history.
function stripKeyFromAddressBar(fragment) {
  try { sessionStorage.setItem(SS_KEY, fragment); } catch { /* ignore */ }
  history.replaceState(null, '', window.location.pathname);
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
  const { fragment, fromHash } = readKeyFragment();
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

  // Sensitive docs hide the key from the address bar; shareable docs (default)
  // leave it there so the address bar itself is a working share link.
  if (doc.sensitive && fromHash) {
    stripKeyFromAddressBar(fragment);
  }

  const html = new TextDecoder().decode(plaintext);

  // Paint the parent page (and the frame's letterbox area) to match the document's
  // own background, so a short document doesn't sit on a mismatched backdrop. The
  // frame is sandboxed/cross-origin, so the parent can't read its styles — instead
  // the only thing we add to the document is a tiny READ-ONLY script that reports
  // its computed body background via postMessage. (e.origin is "null" for an opaque
  // sandbox, so we trust by source, not origin.) Everything else renders as authored.
  window.addEventListener('message', (e) => {
    if (e.source !== frame.contentWindow || !e.data || typeof e.data.__hcbg !== 'string') return;
    const c = e.data.__hcbg;
    if (c && c !== 'transparent' && c !== 'rgba(0, 0, 0, 0)') {
      document.body.style.background = c;
      frame.style.background = c;
    }
  });

  const bgReporter =
    '<scr' + 'ipt>(function(){function s(){try{parent.postMessage({__hcbg:getComputedStyle(document.body).backgroundColor},"*")}catch(e){}}' +
    'if(document.readyState!=="loading")s();else addEventListener("DOMContentLoaded",s);addEventListener("load",s)})()<\/scr' + 'ipt>';
  const injected = /<head[^>]*>/i.test(html)
    ? html.replace(/<head[^>]*>/i, (m) => m + bgReporter)
    : bgReporter + html;

  // srcdoc works in sandboxed iframes without allow-same-origin.
  frame.srcdoc = injected;
  frame.classList.remove('hidden');
  loadScreen.classList.add('hidden');

  // The floating badge always offers Copy link. If this device uploaded the doc,
  // it also links to "Your uploads", where management lives. Nothing about the
  // edit key needs to ride along here.
  // Preserve the cosmetic slug from the address bar so re-shared links keep it.
  const slugSeg  = location.pathname.split('/')[3] || '';
  const viewPath = slugSeg ? `/v/${docId}/${slugSeg}` : `/v/${docId}`;
  const shareUrl = `${window.location.origin}${viewPath}#${b64url(viewKeyRaw)}`;
  setupBadge(shareUrl, !!getUpload(docId));

  // Just uploaded from this tab? Greet the creator once, and explain sharing.
  let justUploaded = false;
  try { justUploaded = sessionStorage.getItem('hc_just_uploaded') === docId; } catch { /* ignore */ }
  if (justUploaded) {
    try { sessionStorage.removeItem('hc_just_uploaded'); } catch { /* ignore */ }
    showUploadToast(shareUrl, doc.sensitive);
  }
}

// One-time confirmation shown to the creator right after upload.
function showUploadToast(shareUrl, sensitive) {
  const toast = document.getElementById('upload-toast');
  if (!toast) return;
  const sub     = document.getElementById('upload-toast-sub');
  const copyBtn = document.getElementById('upload-toast-copy');

  sub.textContent = sensitive
    ? 'Share it with Copy link — the key stays hidden from the address bar.'
    : 'Your link is in the address bar — or use Copy link.';

  copyBtn.addEventListener('click', () => {
    copyToClipboard(shareUrl);
    copyBtn.textContent = 'Copied';
    setTimeout(() => { copyBtn.textContent = 'Copy link'; }, 1800);
  });

  const dismiss = () => toast.classList.add('upload-toast-leaving');
  document.getElementById('upload-toast-dismiss').addEventListener('click', dismiss);

  requestAnimationFrame(() => toast.classList.remove('hidden'));
  setTimeout(dismiss, 9000); // auto-dismiss; never blocks the document
}

// The floating lock pill that lives in the parent viewer page (above the iframe).
// Quiet at rest — just a lock glyph; on hover/focus/tap it expands to reveal the
// attribution and Copy link. Because it's outside the sandboxed frame it can't be
// covered, removed, or spoofed by the document.
function setupBadge(shareUrl, isOwner) {
  const badge     = document.getElementById('hc-badge');
  if (!badge) return;
  const inner     = badge.querySelector('.hc-badge-inner');
  const lock      = badge.querySelector('.hc-badge-lock');
  const copy      = badge.querySelector('.hc-badge-copy');
  const copyLabel = badge.querySelector('.hc-badge-copy-label');
  const manage    = badge.querySelector('.hc-badge-manage');

  // Owner-only (this device uploaded it): surface the "Your uploads" link, where
  // management lives. The badge text stays neutral for everyone.
  if (isOwner && manage) {
    manage.classList.remove('hidden');
  }

  badge.classList.remove('hidden');

  const collapse = () => {
    inner.classList.remove('open');
    lock.setAttribute('aria-expanded', 'false');
  };

  // Touch has no hover-out, so the lock is a tap target that toggles the pill.
  lock.addEventListener('click', () => {
    const open = inner.classList.toggle('open');
    lock.setAttribute('aria-expanded', open ? 'true' : 'false');
    if (!open) lock.blur();
  });

  copy.addEventListener('click', () => {
    copyToClipboard(shareUrl);
    const original = copyLabel.textContent;
    copyLabel.textContent = 'Link copied';
    setTimeout(() => { copyLabel.textContent = original; }, 1800);
  });

  // Dismiss when the visitor turns to the document. Focus moving into the iframe
  // blurs the parent window — the very case the old in-frame badge couldn't catch
  // on touch — and a tap anywhere outside the badge collapses it too.
  window.addEventListener('blur', collapse);
  document.addEventListener('pointerdown', (e) => { if (!badge.contains(e.target)) collapse(); });
}

async function copyToClipboard(text) {
  try { await navigator.clipboard.writeText(text); }
  catch {
    const ta = Object.assign(document.createElement('textarea'), { value: text, style: 'position:fixed;opacity:0' });
    document.body.appendChild(ta); ta.select(); document.execCommand('copy'); ta.remove();
  }
}

main();
