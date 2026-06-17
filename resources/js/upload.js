import {
  generateViewKey, generateEditKey, exportViewKey,
  encryptBytes, encryptViewKeyWithEditKey, computeEditAuth,
  packCiphertext, b64url,
} from './crypto.js';
import { saveUpload } from './uploads-store.js';

const MAX_SIZE = 10 * 1024 * 1024; // 10 MB

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

    // 1. Generate keys
    const viewKey     = await generateViewKey();
    const editKeyRaw  = await generateEditKey();
    const viewKeyRaw  = await exportViewKey(viewKey);

    // 2. Encrypt content
    const { iv, ciphertext } = await encryptBytes(viewKey, plaintext);
    const packed             = packCiphertext(iv, ciphertext);

    // 3. Encrypt viewKey with editKey (server stores this, can't read it)
    const encryptedViewKey = await encryptViewKeyWithEditKey(viewKeyRaw, editKeyRaw);

    // 4. Auth hash (server verifies edit requests against this)
    const editAuth = await computeEditAuth(editKeyRaw);

    // 5. Upload
    const csrf = document.querySelector('meta[name="csrf-token"]').content;
    const res  = await fetch('/api/documents', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
      body: JSON.stringify({
        ciphertext:          packed,
        encrypted_view_key:  encryptedViewKey,
        edit_auth:           editAuth,
        expires_in:          EXPIRES_IN,
        size:                plaintext.length,
        sensitive,
      }),
    });

    if (!res.ok) {
      if (res.status === 429) throw new Error('Too many uploads — please wait a few minutes and try again.');
      const err = await res.json().catch(() => ({}));
      throw new Error(err.error || 'Upload failed');
    }

    const { id } = await res.json();

    const viewFrag = b64url(viewKeyRaw);
    const editFrag = b64url(editKeyRaw);

    // Remember this upload on THIS device only (never sent to the server) so the
    // owner can find it again and reach Manage — see uploads-store.js.
    saveUpload({ id, viewKey: viewFrag, editKey: editFrag, label: file.name, sensitive });

    // One-shot flag so the viewer can greet the creator with an "uploaded —
    // here's how to share" toast (shown once, only on this device/tab).
    try { sessionStorage.setItem('hc_just_uploaded', id); } catch { /* ignore */ }

    // Land straight on the live document. In the default (shareable) mode the
    // address bar is the working share link; sensitive docs strip it in-viewer.
    // The edit key stays out of this URL — it lives in the device registry.
    window.location.href = `/v/${id}#${viewFrag}`;
  } catch (err) {
    console.error(err);
    uploadingState.classList.add('hidden');
    dropzone.classList.remove('hidden');
    showDropzoneError(err.message);
  }
}
