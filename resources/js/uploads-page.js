import { listUploads, getUpload, removeUpload } from './uploads-store.js';

const listEl  = document.getElementById('uploads-list');
const emptyEl = document.getElementById('uploads-empty');

function fmtDate(ts) {
  try { return new Date(ts).toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' }); }
  catch { return ''; }
}

function esc(s) {
  return String(s).replace(/[&<>"']/g, (c) => (
    { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]
  ));
}

function render() {
  const items = listUploads();
  if (!items.length) {
    emptyEl.classList.remove('hidden');
    listEl.innerHTML = '';
    return;
  }
  emptyEl.classList.add('hidden');

  listEl.innerHTML = items.map((u) => {
    const meta = [u.sensitive ? 'Key hidden' : null, u.createdAt ? fmtDate(u.createdAt) : null]
      .filter(Boolean).join(' · ');
    return `
      <div class="upload-item">
        <div class="upload-item-main">
          <span class="upload-item-label">${esc(u.label || u.id)}</span>
          ${meta ? `<span class="upload-item-meta">${esc(meta)}</span>` : ''}
        </div>
        <div class="upload-item-actions">
          <a class="link-btn link-btn-ghost-sm" href="/v/${esc(u.id)}${u.slug ? '/' + esc(u.slug) : ''}#${esc(u.viewKey)}" target="_blank" rel="noopener">Open</a>
          <a class="link-btn link-btn-ghost-sm" href="/e/${esc(u.id)}#${esc(u.editKey)}">Manage</a>
          ${u.editKey ? `<button type="button" class="upload-item-delete" data-delete="${esc(u.id)}">Delete</button>` : ''}
          <button type="button" class="upload-item-forget" data-forget="${esc(u.id)}">Remove from list</button>
        </div>
      </div>`;
  }).join('');
}

// Two different things, kept visibly apart: Delete removes the document from the
// server so the link stops working; "Remove from list" only drops this browser's
// copy of the keys and leaves the document online.
listEl.addEventListener('click', async (e) => {
  const deleteBtn = e.target.closest('[data-delete]');
  if (deleteBtn) {
    await deleteDocument(getUpload(deleteBtn.dataset.delete), deleteBtn);
    return;
  }

  const forgetBtn = e.target.closest('[data-forget]');
  if (!forgetBtn) return;
  if (!confirm('Remove this from the list on this browser?\n\nThe document stays online and its link keeps working. '
    + 'You won’t be able to update or delete it from here anymore — use Delete instead if you want it gone.')) return;
  removeUpload(forgetBtn.dataset.forget);
  render();
});

async function deleteDocument(upload, btn) {
  if (!upload) return;
  const name = upload.label || upload.id;
  if (!confirm(`Delete “${name}” from html.cloud?\n\nThe link stops working for everyone, immediately. This can’t be undone.`)) return;

  btn.disabled = true;
  btn.textContent = 'Deleting…';
  try {
    const res = await fetch(`/api/documents/${encodeURIComponent(upload.id)}`, {
      method: 'DELETE',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ edit_key: upload.editKey }),
    });
    // 404: already expired or deleted elsewhere — the outcome the person wanted.
    if (!res.ok && res.status !== 404) throw new Error(`HTTP ${res.status}`);
    removeUpload(upload.id);
    render();
  } catch (err) {
    console.error(err);
    btn.disabled = false;
    btn.textContent = 'Delete';
    alert('Could not delete the document. Please try again, or use Manage.');
  }
}

render();
