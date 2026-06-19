/**
 * Service worker — the only context with html.cloud host_permissions, so the
 * cross-origin upload happens here (a content script on a file:// page would be
 * blocked by CORS). It receives the raw file bytes from the content script and
 * runs the exact same encrypt-then-upload path as the website and the CLI.
 */

import { shareDocument, slugify, viewPath } from './vendor/share-core.js';
import { b64urlDecode } from './vendor/crypto.js';
import { BASE_URL } from './config.js';

const PENDING_KEY = 'hc_pending'; // entries waiting to be merged into the site

// First run: walk the user through granting file access. Without it, the
// file:// content script never injects and the floating button can't appear.
chrome.runtime.onInstalled.addListener(({ reason }) => {
  if (reason === 'install') chrome.runtime.openOptionsPage();
});

// Toolbar click is the "am I set up?" entry point — always safe to show status.
chrome.action.onClicked.addListener(() => chrome.runtime.openOptionsPage());

chrome.runtime.onMessage.addListener((msg, _sender, sendResponse) => {
  if (msg?.type === 'hc-share') {
    handleShare(msg).then(sendResponse).catch((err) =>
      sendResponse({ ok: false, error: err?.message || 'Share failed' })
    );
    return true; // keep the message channel open for the async response
  }
});

async function handleShare({ name, data }) {
  const bytes = b64urlDecode(data);

  // Same call the homepage makes — keys generated locally, only ciphertext sent.
  const { id, viewFrag, editFrag } = await shareDocument(bytes, { baseUrl: BASE_URL });

  const slug = slugify(name);

  // Stash the upload so content-merge.js can surface it under "Your uploads" the
  // next time html.cloud is open. Shape matches the site's hc_uploads registry.
  const entry = {
    id,
    viewKey: viewFrag,
    editKey: editFrag,
    label: name,
    slug,
    sensitive: false,
    createdAt: Date.now(),
  };
  const { [PENDING_KEY]: pending = [] } = await chrome.storage.local.get(PENDING_KEY);
  await chrome.storage.local.set({
    [PENDING_KEY]: [entry, ...pending.filter((e) => e.id !== id)],
  });

  const shareUrl = `${BASE_URL}${viewPath(id, slug)}#${viewFrag}`;

  // Land the user on their live shared document, the same payoff the website's
  // upload gives. The fragment key rides along so the new tab decrypts it.
  let opened = false;
  try { await chrome.tabs.create({ url: shareUrl }); opened = true; } catch { /* fall back to in-pill link */ }

  return { ok: true, shareUrl, opened };
}
