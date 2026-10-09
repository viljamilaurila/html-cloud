import { shareDocument, slugify, viewPath } from './share-core.js';
import { saveUpload, listUploads, findPreviousVersion } from './uploads-store.js';
import { replaceUpload, checkHtmlFile } from './replace-upload.js';

const dropzone       = document.getElementById('dropzone');
const fileInput      = document.getElementById('file-input');
const uploadingState = document.getElementById('uploading-state');
// Default expiry; adjustable on the edit page after upload.
const EXPIRES_IN = '30';
// Lets the server count uploads per client (a daily total, nothing more).
const CLIENT_HEADER = { 'X-HTML-Cloud-Client': 'web' };

// Web Crypto requires a secure context (HTTPS or localhost/127.0.0.1).
if (!window.isSecureContext || !window.crypto?.subtle) {
  dropzone.innerHTML = `
    <div style="color:var(--warn);font-size:14px;line-height:1.6;text-align:center;max-width:480px;">
      <strong>HTTPS required</strong><br>
      Encryption uses the browser's Web Crypto API, which only works over HTTPS
      (or <code>localhost</code>). Please access this site via <code>https://</code>.
    </div>`;
  throw new Error('Not a secure context — Web Crypto unavailable.');
}

// ─── Error banner ───
let errorBanner = null;
function showDropzoneError(msg) {
  if (errorBanner) errorBanner.remove();
  errorBanner = document.createElement('div');
  errorBanner.style.cssText = `
    margin-top:16px;padding:12px 16px;background:var(--warn-soft);
    border:1px solid rgba(138,90,31,0.25);border-radius:8px;
    color:var(--warn);font-size:13px;line-height:1.5;
    display:flex;align-items:center;gap:10px;max-width:880px;width:100%;
  `;
  errorBanner.innerHTML = `<span style="flex:1">${msg}</span>
    <button onclick="this.parentElement.remove()" style="background:none;border:none;cursor:pointer;color:var(--warn);font-size:16px;padding:0;line-height:1;">×</button>`;
  dropzone.insertAdjacentElement('afterend', errorBanner);
}

// ─── Drag & drop ───
dropzone.addEventListener('dragover', e => {
  e.preventDefault();
  dropzone.classList.add('drag-over');
});
dropzone.addEventListener('dragleave', e => {
  if (!dropzone.contains(e.relatedTarget)) dropzone.classList.remove('drag-over');
});
dropzone.addEventListener('drop', e => {
  e.preventDefault();
  dropzone.classList.remove('drag-over');
  const file = e.dataTransfer?.files?.[0];
  if (file) handleFile(file);
});

fileInput.addEventListener('change', () => {
  if (fileInput.files?.[0]) handleFile(fileInput.files[0]);
});

// ─── New version of something already shared? ───
// Matched by filename against this browser's own upload list. Updating keeps the
// link people already have, which is what someone dropping "report (1).html"
// almost always wants — but they choose.
const versionPrompt = document.getElementById('version-prompt');

async function handleFile(file) {
  const problem = checkHtmlFile(file);
  if (problem) {
    alert(problem);
    return;
  }

  const previous = versionPrompt ? findPreviousVersion(file.name) : null;
  if (previous) {
    askAboutPreviousVersion(file, previous);
    return;
  }

  await shareAsNew(file);
}

function askAboutPreviousVersion(file, previous) {
  document.getElementById('version-prompt-name').textContent = previous.label || 'your earlier upload';
  document.getElementById('version-prompt-when').textContent = sharedWhen(previous);

  dropzone.classList.add('hidden');
  versionPrompt.classList.remove('hidden');

  const close = () => {
    versionPrompt.classList.add('hidden');
    for (const id of ['version-prompt-update', 'version-prompt-new', 'version-prompt-cancel']) {
      document.getElementById(id).onclick = null;
    }
  };

  document.getElementById('version-prompt-update').onclick = () => { close(); updateExisting(previous, file); };
  document.getElementById('version-prompt-new').onclick    = () => { close(); shareAsNew(file); };
  document.getElementById('version-prompt-cancel').onclick = () => {
    close();
    dropzone.classList.remove('hidden');
    fileInput.value = '';
  };
  document.getElementById('version-prompt-update').focus();
}

/** Replace an upload this device owns and land on it, with the same link. */
async function updateExisting(upload, file) {
  dropzone.classList.add('hidden');
  uploadingState.classList.remove('hidden');
  try {
    await replaceUpload(upload, file);
    try { sessionStorage.setItem('hc_just_updated', upload.id); } catch { /* ignore */ }
    window.location.href = `${viewPath(upload.id, upload.slug)}#${upload.viewKey}`;
  } catch (err) {
    console.error(err);
    uploadingState.classList.add('hidden');
    dropzone.classList.remove('hidden');
    showDropzoneError(err.gone ? `${err.message} Drop the file again to share it as a new link.` : err.message);
    renderRecentUploads();
  }
}

function sharedWhen(upload) {
  const ts = upload.updatedAt || upload.createdAt;
  if (!ts) return 'earlier';
  const days = Math.floor((Date.now() - ts) / 86400000);
  if (days < 1) return 'today';
  if (days === 1) return 'yesterday';
  return 'on ' + new Date(ts).toLocaleDateString(undefined, { month: 'short', day: 'numeric' });
}

// ─── Recent uploads (home page only) ───
const recentSection = document.getElementById('recent-uploads');
const recentList    = document.getElementById('recent-uploads-list');
const recentFile    = document.getElementById('recent-uploads-file');
const RECENT_LIMIT  = 3;
let recentTarget    = null;

function esc(s) {
  return String(s).replace(/[&<>"']/g, (c) => (
    { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]
  ));
}

function renderRecentUploads() {
  if (!recentSection) return;
  const items = listUploads().filter((u) => u.editKey).slice(0, RECENT_LIMIT);
  recentSection.classList.toggle('hidden', items.length === 0);
  recentList.innerHTML = items.map((u) => `
    <li class="recent-upload">
      <a class="recent-upload-name" href="${esc(viewPath(u.id, u.slug))}#${esc(u.viewKey)}">${esc(u.label || u.id)}</a>
      <span class="recent-upload-when">${esc(sharedWhen(u))}</span>
      <button type="button" class="link-btn link-btn-ghost-sm" data-update="${esc(u.id)}">Update…</button>
    </li>`).join('');
}

if (recentSection) {
  recentList.addEventListener('click', (e) => {
    const btn = e.target.closest('[data-update]');
    if (!btn) return;
    recentTarget = listUploads().find((u) => u.id === btn.dataset.update) || null;
    if (recentTarget) recentFile.click();
  });
  recentFile.addEventListener('change', () => {
    const file = recentFile.files?.[0];
    recentFile.value = '';
    if (!file || !recentTarget) return;
    const problem = checkHtmlFile(file);
    if (problem) { alert(problem); return; }
    updateExisting(recentTarget, file);
  });
  renderRecentUploads();
}

async function shareAsNew(file) {
  // Home uploads are always shareable links; the extra-private (fragment-stripping)
  // mode can still be switched on from the document's edit page.
  const sensitive = false;

  dropzone.classList.add('hidden');
  uploadingState.classList.remove('hidden');

  try {
    const plaintext = new Uint8Array(await file.arrayBuffer());

    // Encrypt locally and upload ciphertext only — shared with the CLI and the
    // browser extension via share-core.js so the wire contract never diverges.
    const { id, viewFrag, editFrag } = await shareDocument(plaintext, {
      expiresIn: EXPIRES_IN,
      sensitive,
      headers: CLIENT_HEADER,
    });

    // Sensitive docs keep the filename out of the URL/preview entirely; shareable
    // docs get a cosmetic slug so links are self-describing and preview a title.
    const slug = sensitive ? '' : slugify(file.name);

    // Remember this upload on THIS device only (never sent to the server) so the
    // owner can find it again and reach Manage — see uploads-store.js.
    saveUpload({ id, viewKey: viewFrag, editKey: editFrag, label: file.name, slug, sensitive });

    // One-shot flag so the viewer can greet the creator with an "uploaded —
    // here's how to share" toast (shown once, only on this device/tab).
    try { sessionStorage.setItem('hc_just_uploaded', id); } catch { /* ignore */ }

    // Land straight on the live document. In the default (shareable) mode the
    // address bar is the working share link; sensitive docs strip it in-viewer.
    // The edit key stays out of this URL — it lives in the device registry.
    window.location.href = `${viewPath(id, slug)}#${viewFrag}`;
  } catch (err) {
    console.error(err);
    uploadingState.classList.add('hidden');
    dropzone.classList.remove('hidden');
    showDropzoneError(err.message);
  }
}
