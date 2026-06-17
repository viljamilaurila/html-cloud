/**
 * Device-local registry of documents uploaded from THIS browser.
 *
 * Zero-knowledge means the server can't tell you which documents are yours, and
 * can't recover your keys. So the convenience of "find my uploads again" and
 * "manage this doc" lives entirely here, in localStorage — never on the server,
 * and (unlike browser history) never synced to Google.
 *
 * Each entry holds both keys so we can offer Open and Manage links:
 *   { id, viewKey, editKey, label, sensitive, createdAt }
 * viewKey / editKey are the base64url fragment strings (not raw bytes).
 */

const KEY = 'hc_uploads';

export function listUploads() {
  try {
    const raw = JSON.parse(localStorage.getItem(KEY));
    return Array.isArray(raw) ? raw : [];
  } catch {
    return [];
  }
}

export function getUpload(id) {
  return listUploads().find((u) => u.id === id) || null;
}

export function saveUpload(entry) {
  const rest = listUploads().filter((u) => u.id !== entry.id);
  const record = { createdAt: Date.now(), ...entry };
  try {
    localStorage.setItem(KEY, JSON.stringify([record, ...rest]));
  } catch { /* storage full or unavailable — non-fatal */ }
}

export function removeUpload(id) {
  try {
    localStorage.setItem(KEY, JSON.stringify(listUploads().filter((u) => u.id !== id)));
  } catch { /* ignore */ }
}
