import {
  importViewKey, encryptBytes, encryptViewKeyWithEditKey,
  decryptViewKeyWithEditKey, packCiphertext, b64url, b64urlDecode,
} from './crypto.js';

if (!window.isSecureContext || !window.crypto?.subtle) {
  document.getElementById('editor-ui').innerHTML = `
    <div style="color:var(--warn);font-size:14px;line-height:1.6;text-align:center;max-width:480px;margin:64px auto;">
      <strong>HTTPS required</strong><br>
      The editor uses the browser's Web Crypto API, which only works over HTTPS.
    </div>`;
  throw new Error('Not a secure context.');
}

const docId       = window.__DOC_ID__;
const authError   = document.getElementById('auth-error');
const editorUi    = document.getElementById('editor-ui');
const shareUrlEl  = document.getElementById('share-url');
const shareActions = document.getElementById('share-actions');
const expiryRow   = document.getElementById('expiry-row');
const expiryText  = document.getElementById('expiry-text');
const dropzone    = document.getElementById('dropzone');
const fileInput   = document.getElementById('file-input');
const uploadingState = document.getElementById('uploading-state');
const updateSuccess  = document.getElementById('update-success');

const MONTHS = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
function fmtDate(d) { return `${MONTHS[d.getMonth()]} ${d.getDate()}, ${d.getFullYear()}`; }
function chipLabel(days) {
  const d = new Date(Date.now() + parseInt(days) * 86400000);
  return `${MONTHS[d.getMonth()]} ${d.getDate()}`;
}

// ─── Parse fragment ───

const fragment = window.location.hash.slice(1);
if (!fragment) {
  authError.classList.remove('hidden');
  editorUi.classList.add('hidden');
  throw new Error('No edit key in URL');
}

let editKeyRaw;
try {
  editKeyRaw = b64urlDecode(fragment);
} catch {
  authError.classList.remove('hidden');
  editorUi.classList.add('hidden');
  throw new Error('Could not decode edit key');
}

// ─── Load document + derive share URL ───

async function loadDocument() {
  const res = await fetch(`/api/documents/${docId}`);
  if (!res.ok) {
    authError.classList.remove('hidden');
    editorUi.classList.add('hidden');
    return null;
  }
  return res.json();
}

async function init() {
  const doc = await loadDocument();
  if (!doc) return;

  let viewKeyRaw;
  try {
    viewKeyRaw = await decryptViewKeyWithEditKey(doc.encrypted_view_key, editKeyRaw);
  } catch {
    authError.classList.remove('hidden');
    editorUi.classList.add('hidden');
    return;
  }

  const shareUrl  = `${location.origin}/v/${docId}#${b64url(viewKeyRaw)}`;
  const manageUrl = window.location.href;

  // Show share link. The copied/displayed link stays clean; only the "Open"
  // button tags the URL with ?owner so the viewer can recognise the creator
  // and remind them to share via Copy link rather than the address bar.
  shareUrlEl.textContent = shareUrl;
  document.getElementById('open-share').href =
    `${location.origin}/v/${docId}?owner=1#${b64url(viewKeyRaw)}`;
  shareActions.style.opacity = '1';
  shareActions.style.pointerEvents = 'auto';
  document.getElementById('share-card-hint').style.opacity = '1';

  // Show management link
  document.getElementById('manage-url').textContent = manageUrl;
  document.getElementById('manage-card').style.opacity = '1';
  document.getElementById('copy-manage').addEventListener('click', function () {
    copyText(manageUrl, this);
  });

  // Expiry chips
  document.querySelectorAll('.expiry-chip-sm[data-value]').forEach(chip => {
    if (chip.dataset.value !== 'never') chip.textContent = chipLabel(chip.dataset.value);
  });

  function setExpiryDisplay(isoOrNull) {
    expiryText.textContent = isoOrNull
      ? `Expires ${fmtDate(new Date(isoOrNull))}`
      : 'No expiry';
    const activeVal = isoOrNull
      ? (Math.round((new Date(isoOrNull) - Date.now()) / 86400000) <= 7 ? '7' : '30')
      : 'never';
    document.querySelectorAll('.expiry-chip-sm').forEach(c =>
      c.classList.toggle('active', c.dataset.value === activeVal));
  }

  setExpiryDisplay(doc.expires_at);
  expiryRow.style.opacity = '1';

  // Expiry change
  document.querySelectorAll('.expiry-chip-sm').forEach(chip => {
    chip.addEventListener('click', async () => {
      const csrf = document.querySelector('meta[name="csrf-token"]').content;
      try {
        const r = await fetch(`/api/documents/${docId}/expiry`, {
          method: 'PATCH',
          headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
          body: JSON.stringify({ expires_in: chip.dataset.value, edit_key: b64url(editKeyRaw) }),
        });
        if (!r.ok) throw new Error();
        const { expires_at } = await r.json();
        setExpiryDisplay(expires_at);
      } catch { console.error('Expiry update failed'); }
    });
  });

  // Copy share link
  document.getElementById('copy-share').addEventListener('click', function () {
    copyText(shareUrl, this);
  });

  // Replace file — drag & drop
  dropzone.addEventListener('dragover', e => { e.preventDefault(); dropzone.classList.add('drag-over'); });
  dropzone.addEventListener('dragleave', e => { if (!dropzone.contains(e.relatedTarget)) dropzone.classList.remove('drag-over'); });
  dropzone.addEventListener('drop', e => { e.preventDefault(); dropzone.classList.remove('drag-over'); const f = e.dataTransfer?.files?.[0]; if (f) replaceFile(f, viewKeyRaw); });
  fileInput.addEventListener('change', () => { if (fileInput.files?.[0]) replaceFile(fileInput.files[0], viewKeyRaw); });

  // Delete
  document.getElementById('delete-btn').addEventListener('click', async () => {
    if (!confirm('Delete this document? The share link will stop working immediately.')) return;
    const csrf = document.querySelector('meta[name="csrf-token"]').content;
    try {
      const r = await fetch(`/api/documents/${docId}`, {
        method: 'DELETE',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
        body: JSON.stringify({ edit_key: b64url(editKeyRaw) }),
      });
      if (r.status === 403) { alert('Invalid edit key.'); return; }
      if (!r.ok) throw new Error();
      window.location.href = '/';
    } catch { alert('Could not delete the document. Please try again.'); }
  });
}

init();

// ─── Replace file ───

const MAX_SIZE = 10 * 1024 * 1024;

async function replaceFile(file, viewKeyRaw) {
  if (!file.name.match(/\.html?$/i)) { alert('Please drop an HTML file (.html or .htm).'); return; }
  if (file.size > MAX_SIZE) { alert('File is too large. Maximum size is 10 MB.'); return; }

  dropzone.classList.add('hidden');
  updateSuccess.classList.add('hidden');
  uploadingState.classList.remove('hidden');

  try {
    const viewKey = await importViewKey(viewKeyRaw);
    const plaintext = new Uint8Array(await file.arrayBuffer());
    const { iv, ciphertext } = await encryptBytes(viewKey, plaintext);
    const packed = packCiphertext(iv, ciphertext);
    const encryptedViewKey = await encryptViewKeyWithEditKey(viewKeyRaw, editKeyRaw);

    const csrf = document.querySelector('meta[name="csrf-token"]').content;
    const r = await fetch(`/api/documents/${docId}`, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
      body: JSON.stringify({
        ciphertext: packed,
        encrypted_view_key: encryptedViewKey,
        edit_key: b64url(editKeyRaw),
        size: plaintext.length,
      }),
    });

    if (r.status === 403) { authError.classList.remove('hidden'); editorUi.classList.add('hidden'); return; }
    if (!r.ok) throw new Error('Upload failed');

    uploadingState.classList.add('hidden');
    dropzone.classList.remove('hidden');
    updateSuccess.classList.remove('hidden');
  } catch (err) {
    console.error(err);
    uploadingState.classList.add('hidden');
    dropzone.classList.remove('hidden');
    alert('Something went wrong: ' + err.message);
  }
}

// ─── Copy ───

async function copyText(text, btn) {
  try { await navigator.clipboard.writeText(text); }
  catch {
    const ta = Object.assign(document.createElement('textarea'), { value: text, style: 'position:fixed;opacity:0' });
    document.body.appendChild(ta); ta.select(); document.execCommand('copy'); ta.remove();
  }
  const orig = btn.innerHTML;
  btn.textContent = 'Copied!';
  setTimeout(() => { btn.innerHTML = orig; }, 1800);
}
