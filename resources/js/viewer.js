import { importViewKey, decryptBytes, unpackCiphertext, b64url, b64urlDecode } from './crypto.js';
import { getUpload } from './uploads-store.js';
import { instrumentDocument } from './frame-shim.js';
import { replaceUpload, checkHtmlFile } from './replace-upload.js';

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
    // Identifies this as someone opening the link, for the daily "opens" total.
    const res = await fetch(`/api/documents/${docId}`, { headers: { 'X-HTML-Cloud-Client': 'viewer' } });
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

  // Paint the parent page (and the frame's letterbox area) to match the document's
  // own background, so a short document doesn't sit on a mismatched backdrop. The
  // frame is sandboxed/cross-origin, so the parent can't read its styles — instead
  // the injected shim (see frame-shim.js) reports the computed body background via
  // postMessage. (e.origin is "null" for an opaque sandbox, so we trust by source,
  // not origin.) The same shim also keeps in-page #anchor links working inside the
  // srcdoc frame; everything else renders as authored.
  window.addEventListener('message', (e) => {
    if (e.source !== frame.contentWindow || !e.data || typeof e.data.__hcbg !== 'string') return;
    const c = e.data.__hcbg;
    if (c && c !== 'transparent' && c !== 'rgba(0, 0, 0, 0)') {
      document.body.style.background = c;
      frame.style.background = c;
    }
  });

  renderDocument(plaintext);
  frame.classList.remove('hidden');
  loadScreen.classList.add('hidden');

  // The floating badge always offers Copy link and Download. If this device
  // uploaded the doc, it also links to "Your uploads", where management lives.
  // Nothing about the edit key needs to ride along here.
  // Preserve the cosmetic slug from the address bar so re-shared links keep it.
  const slugSeg  = location.pathname.split('/')[3] || '';
  const viewPath = slugSeg ? `/v/${docId}/${slugSeg}` : `/v/${docId}`;
  const shareUrl = `${window.location.origin}${viewPath}#${b64url(viewKeyRaw)}`;
  const upload   = getUpload(docId);

  // Download saves the ORIGINAL plaintext, never the instrumented copy — the shim
  // is our rendering instrumentation and must never end up in the saved file.
  setupBadge(shareUrl, !!upload, () => downloadDocument(currentPlaintext, downloadFilename(getUpload(docId), slugSeg)));
  setupToast(shareUrl);

  // This browser uploaded it: offer replacing it in place (badge + drop anywhere).
  if (upload?.editKey) setupReplace(upload);

  // Just uploaded or updated from this tab? Greet the creator once.
  const greeting = takeOnce('hc_just_uploaded') ? 'uploaded' : takeOnce('hc_just_updated') ? 'updated' : null;
  if (greeting === 'uploaded') {
    showToast('Encrypted & uploaded', doc.sensitive
      ? 'Share it with Copy link — the key stays hidden from the address bar.'
      : 'Your link is in the address bar. New version later? Drop it on this page.');
  } else if (greeting === 'updated') {
    showToast('Updated — same link', 'Everyone with the link now sees this version.');
  }
}

// The plaintext currently on screen; replaced in place when the owner uploads a
// new version, so Download always saves what's showing.
let currentPlaintext = null;

function renderDocument(plaintext) {
  currentPlaintext = plaintext;
  // srcdoc works in sandboxed iframes without allow-same-origin.
  frame.srcdoc = instrumentDocument(new TextDecoder().decode(plaintext));
}

/** True once if this tab left `key` = this doc's id in sessionStorage. */
function takeOnce(key) {
  try {
    if (sessionStorage.getItem(key) !== docId) return false;
    sessionStorage.removeItem(key);
    return true;
  } catch {
    return false;
  }
}

// ─── Replace in place (owner only) ───
// The edit key comes from this device's own upload list, so only the browser that
// shared the document ever sees these controls. Dropping a new file asks once,
// then re-encrypts it under the same link and swaps it in without a reload.
function setupReplace(upload) {
  const overlay   = document.getElementById('replace-drop');
  const confirmEl = document.getElementById('replace-confirm');
  const nameEl    = document.getElementById('replace-confirm-name');
  const okBtn     = document.getElementById('replace-confirm-ok');
  const cancelBtn = document.getElementById('replace-confirm-cancel');
  const badgeBtn  = document.querySelector('.hc-badge-replace');
  const input     = document.querySelector('.hc-badge-replace-input');
  if (!overlay || !confirmEl) return;

  let pending = null;

  const isHtmlDrag = (e) => [...(e.dataTransfer?.items || [])]
    .some((item) => item.kind === 'file' && item.type === 'text/html');

  // While the overlay is up the frame must not be a drop target at all, or a
  // drop that lands on it opens the raw file instead of replacing the page.
  // dragleave is unreliable here (relatedTarget is often null across the iframe
  // boundary, so the overlay flickered away), so instead the overlay stays up as
  // long as dragover keeps arriving — browsers fire it continuously — and hides
  // shortly after it stops: on drop, Esc, or the pointer leaving the window.
  let hideTimer = null;
  const hideOverlay = () => {
    clearTimeout(hideTimer);
    overlay.classList.add('hidden');
    frame.style.pointerEvents = '';
  };
  const showOverlay = () => {
    if (pending) return;
    overlay.classList.remove('hidden');
    frame.style.pointerEvents = 'none';
    clearTimeout(hideTimer);
    hideTimer = setTimeout(hideOverlay, 400);
  };

  // Over the frame the parent gets no drag events, so the injected shim reports
  // them; over the parent's own edges we see them directly.
  window.addEventListener('message', (e) => {
    if (e.source === frame.contentWindow && e.data && e.data.__hcdrag) showOverlay();
  });
  document.addEventListener('dragenter', (e) => { if (isHtmlDrag(e)) showOverlay(); });

  overlay.addEventListener('dragover', (e) => {
    e.preventDefault();
    e.dataTransfer.dropEffect = 'copy';
    showOverlay();
  });
  overlay.addEventListener('drop', (e) => {
    e.preventDefault();
    hideOverlay();
    const file = e.dataTransfer?.files?.[0];
    if (file) ask(file);
  });
  // Never let a stray drop navigate the tab away to the raw file.
  window.addEventListener('dragover', (e) => e.preventDefault());
  window.addEventListener('drop', (e) => e.preventDefault());

  badgeBtn.classList.remove('hidden');
  badgeBtn.addEventListener('click', () => input.click());
  input.addEventListener('change', () => {
    const file = input.files?.[0];
    input.value = '';
    if (file) ask(file);
  });

  function ask(file) {
    const problem = checkHtmlFile(file);
    if (problem) { alert(problem); return; }
    pending = file;
    nameEl.textContent = file.name;
    confirmEl.classList.remove('hidden');
    okBtn.focus();
  }

  function close() {
    pending = null;
    confirmEl.classList.add('hidden');
    okBtn.disabled = false;
    okBtn.textContent = 'Replace page';
  }

  cancelBtn.addEventListener('click', close);
  // Esc or a click on the dimmed backdrop cancels, like any dialog.
  confirmEl.addEventListener('click', (e) => { if (e.target === confirmEl && !okBtn.disabled) close(); });
  document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && pending && !okBtn.disabled) close(); });
  okBtn.addEventListener('click', async () => {
    if (!pending) return;
    okBtn.disabled = true;
    okBtn.textContent = 'Encrypting…';
    try {
      const owned = getUpload(docId) || upload;
      renderDocument(await replaceUpload(owned, pending));
      close();
      showToast('Updated — same link', 'Everyone with the link now sees this version.');
    } catch (err) {
      console.error(err);
      close();
      if (err.gone) {
        badgeBtn.classList.add('hidden');
        hideOverlay();
      }
      alert(err.message);
    }
  });
}

// Save the document to disk. Everything happens on bytes we already hold in
// memory — no server round-trip, nothing to ask permission for, and the file
// lands byte-identical to what was uploaded.
function downloadDocument(bytes, filename) {
  const url = URL.createObjectURL(new Blob([bytes], { type: 'text/html;charset=utf-8' }));
  const a = Object.assign(document.createElement('a'), { href: url, download: filename });
  document.body.appendChild(a);
  a.click();
  a.remove();
  // Revoke a tick later, once the browser has claimed the blob.
  setTimeout(() => URL.revokeObjectURL(url), 0);
}

// The server never learns the original filename — storing it would leak the one
// descriptive thing about an otherwise opaque record — so recover the best name
// available, in order: the device-local upload registry (exact, but only on the
// uploader's own machine), then the cosmetic slug in the URL, then the doc id.
function downloadFilename(upload, slugSeg) {
  const raw = upload?.label || (slugSeg ? `${slugSeg}.html` : `html-cloud-${docId}.html`);
  // The label comes from localStorage, so treat it as untrusted: keep the last
  // path segment only, and drop anything a filesystem would object to.
  const base = raw.split(/[\\/]/).pop().replace(/[<>:"|?*]/g, '').trim();
  if (!base || base === '.html') return `html-cloud-${docId}.html`;
  return /\.html?$/i.test(base) ? base : `${base}.html`;
}

// Confirmation shown to the creator after an upload or an update. Wired once;
// showToast() can then raise it as often as needed.
let toastTimer = null;

function setupToast(shareUrl) {
  const toast   = document.getElementById('upload-toast');
  const copyBtn = document.getElementById('upload-toast-copy');
  if (!toast) return;

  copyBtn.addEventListener('click', () => {
    copyToClipboard(shareUrl);
    copyBtn.textContent = 'Copied';
    setTimeout(() => { copyBtn.textContent = 'Copy link'; }, 1800);
  });
  document.getElementById('upload-toast-dismiss').addEventListener('click', dismissToast);
}

function showToast(title, sub) {
  const toast = document.getElementById('upload-toast');
  if (!toast) return;
  document.getElementById('upload-toast-title').textContent = title;
  document.getElementById('upload-toast-sub').textContent = sub;
  toast.classList.remove('upload-toast-leaving');
  requestAnimationFrame(() => toast.classList.remove('hidden'));
  clearTimeout(toastTimer);
  toastTimer = setTimeout(dismissToast, 9000); // auto-dismiss; never blocks the document
}

function dismissToast() {
  document.getElementById('upload-toast')?.classList.add('upload-toast-leaving');
}

// The floating lock pill that lives in the parent viewer page (above the iframe).
// Quiet at rest — just a lock glyph; on hover/focus/tap it expands to reveal the
// attribution, Copy link and Download. Because it's outside the sandboxed frame
// it can't be covered, removed, or spoofed by the document — which is also why
// Download lives here rather than in a control the document could impersonate.
function setupBadge(shareUrl, isOwner, onDownload) {
  const badge     = document.getElementById('hc-badge');
  if (!badge) return;
  const inner     = badge.querySelector('.hc-badge-inner');
  const lock      = badge.querySelector('.hc-badge-lock');
  const copy      = badge.querySelector('.hc-badge-copy');
  const copyLabel = badge.querySelector('.hc-badge-copy-label');
  const download  = badge.querySelector('.hc-badge-download');
  const dlLabel   = badge.querySelector('.hc-badge-download-label');
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

  download.addEventListener('click', () => {
    onDownload();
    const original = dlLabel.textContent;
    dlLabel.textContent = 'Saved';
    setTimeout(() => { dlLabel.textContent = original; }, 1800);
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
