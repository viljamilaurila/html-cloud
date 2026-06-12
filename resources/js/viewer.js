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

  // Render the document exactly as authored — we no longer inject anything into
  // the (untrusted, sandboxed) frame. The html.cloud badge lives in the parent
  // viewer page instead (see setupBadge below), so the document's own scripts,
  // overlays, and corner widgets can't cover, remove, or spoof it.
  // srcdoc works in sandboxed iframes without allow-same-origin.
  frame.srcdoc = html;
  frame.classList.remove('hidden');
  loadScreen.classList.add('hidden');

  // The key decrypted cleanly — remember it for this tab so a reload still works
  // even though the address bar no longer carries it.
  if (fromHash) {
    try { sessionStorage.setItem(SS_KEY, fragment); } catch { /* ignore */ }
  }

  // The address bar is now key-free, so the parent page is the only place that
  // still holds the full share link. Reveal the floating badge and wire its
  // Copy link control directly to it.
  const shareUrl = `${window.location.origin}/v/${docId}#${b64url(viewKeyRaw)}`;
  setupBadge(shareUrl);

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

// The floating lock pill that lives in the parent viewer page (above the iframe).
// Quiet at rest — just a lock glyph; on hover/focus/tap it expands to reveal the
// attribution and Copy link. Because it's outside the sandboxed frame it can't be
// covered, removed, or spoofed by the document.
function setupBadge(shareUrl) {
  const badge     = document.getElementById('hc-badge');
  if (!badge) return;
  const inner     = badge.querySelector('.hc-badge-inner');
  const lock      = badge.querySelector('.hc-badge-lock');
  const copy      = badge.querySelector('.hc-badge-copy');
  const copyLabel = badge.querySelector('.hc-badge-copy-label');

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
