import { shareDocument, slugify, viewPath, MAX_SIZE } from './share-core.js';
import { saveUpload } from './uploads-store.js';

const dropzone       = document.getElementById('dropzone');
const fileInput      = document.getElementById('file-input');
const uploadingState = document.getElementById('uploading-state');
// Default expiry; adjustable on the edit page after upload.
const EXPIRES_IN = '30';

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

async function handleFile(file) {
  if (!file.name.match(/\.html?$/i)) {
    alert('Please drop an HTML file (.html or .htm).');
    return;
  }
  if (file.size > MAX_SIZE) {
    alert('File is too large. Maximum size is 10 MB.');
    return;
  }

  const sensitive = !!document.getElementById('sensitive-toggle')?.checked;

  dropzone.classList.add('hidden');
  document.getElementById('upload-options')?.classList.add('hidden');
  uploadingState.classList.remove('hidden');

  try {
    const plaintext = new Uint8Array(await file.arrayBuffer());

    // Encrypt locally and upload ciphertext only — shared with the CLI and the
    // browser extension via share-core.js so the wire contract never diverges.
    const csrf = document.querySelector('meta[name="csrf-token"]').content;
    const { id, viewFrag, editFrag } = await shareDocument(plaintext, {
      expiresIn: EXPIRES_IN,
      sensitive,
      headers: { 'X-CSRF-TOKEN': csrf },
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
