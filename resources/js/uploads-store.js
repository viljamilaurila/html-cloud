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

/**
 * Reduce a filename to the document it most likely is, so a new version still
 * matches the upload it replaces: "Report (1).html", "report-v2.html" and
 * "report final.htm" all become "report". Only ever used to *suggest* an update —
 * the person always confirms — so a loose match is cheap and a missed one isn't.
 */
export function documentStem(fileName) {
  let stem = String(fileName || '').split(/[\\/]/).pop().toLowerCase().replace(/\.html?$/, '').trim();
  let previous;
  do {
    previous = stem;
    stem = stem
      .replace(/\s*\(\d+\)$/, '')                                  // browser duplicate: "report (1)"
      .replace(/[\s_-]+(v(er(sion)?)?\s*\d+|\d+|copy|final|updated|new|latest)$/, '') // "report-v2", "report final"
      .trim();
  } while (stem && stem !== previous);
  return stem;
}

/**
 * The most recent upload from this device that the dropped file looks like a new
 * version of, or null. `uploads` is injectable for tests.
 */
export function findPreviousVersion(fileName, uploads = listUploads()) {
  const stem = documentStem(fileName);
  if (!stem) return null;
  return uploads.find((u) => u.editKey && documentStem(u.label) === stem) || null;
}

export function removeUpload(id) {
  try {
    localStorage.setItem(KEY, JSON.stringify(listUploads().filter((u) => u.id !== id)));
  } catch { /* ignore */ }
}
