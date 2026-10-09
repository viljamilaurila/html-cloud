import { updateDocument, MAX_SIZE } from './share-core.js';
import { saveUpload, removeUpload } from './uploads-store.js';

/**
 * Put a new version of a file behind an upload this device owns. The share link
 * stays the same, so everyone who has it sees the new version. Used by the home
 * page ("update your earlier link?") and by the viewer (owner drops a file on the
 * open document); the wire format lives in share-core.js like every other client.
 *
 * Throws an Error with a user-facing message. `gone` is set when the document no
 * longer exists, after it has been dropped from this device's list.
 *
 * @param {{ id: string, editKey: string }} upload  Entry from uploads-store.js.
 * @param {File} file
 * @returns {Promise<Uint8Array>} The new plaintext, for re-rendering in place.
 */
export async function replaceUpload(upload, file) {
  const problem = checkHtmlFile(file);
  if (problem) throw new Error(problem);

  const plaintext = new Uint8Array(await file.arrayBuffer());

  try {
    await updateDocument(upload.id, upload.editKey, plaintext);
  } catch (err) {
    if (/not found/i.test(err.message)) {
      removeUpload(upload.id);
      throw Object.assign(new Error('That earlier link has expired or was deleted, so it can’t be updated.'), { gone: true });
    }
    throw err;
  }

  // Keep the cosmetic slug (it's part of the link people already have) but
  // remember the newest filename, so the next version still matches.
  saveUpload({ ...upload, label: file.name, updatedAt: Date.now() });

  return plaintext;
}

/** A user-facing reason the file can't be shared, or null when it's fine. */
export function checkHtmlFile(file) {
  if (!/\.html?$/i.test(file.name)) return 'Please use an HTML file (.html or .htm).';
  if (file.size > MAX_SIZE) return 'File is too large. Maximum size is 10 MB.';
  return null;
}
