import { listUploads, removeUpload } from './uploads-store.js';

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
          <a class="link-btn link-btn-ghost-sm" href="/v/${esc(u.id)}#${esc(u.viewKey)}" target="_blank" rel="noopener">Open</a>
          <a class="link-btn link-btn-ghost-sm" href="/e/${esc(u.id)}#${esc(u.editKey)}">Manage</a>
          <button type="button" class="upload-item-forget" data-forget="${esc(u.id)}">Forget</button>
        </div>
      </div>`;
  }).join('');
}

// "Forget" only removes the entry from this device's list — it does NOT delete
// the document (that's what Manage → Delete is for).
listEl.addEventListener('click', (e) => {
  const btn = e.target.closest('[data-forget]');
  if (!btn) return;
  if (!confirm('Remove this from the list on this device? This won’t delete the document — open Manage to do that.')) return;
  removeUpload(btn.dataset.forget);
  render();
});

render();
