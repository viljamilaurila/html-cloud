/**
 * Injected on html.cloud. Merges uploads created by the extension into the
 * site's device-local "Your uploads" registry (localStorage['hc_uploads']), so
 * files shared from the floating button show up alongside homepage uploads.
 *
 * Content scripts share the host page's localStorage (same origin), so this is a
 * purely client-side, device-local merge — no keys ever touch the server, fully
 * consistent with the zero-knowledge model. One-directional: extension → site.
 */

const PENDING_KEY = 'hc_pending'; // extension's own copy (chrome.storage)
const SITE_KEY = 'hc_uploads';    // the site's registry (page localStorage)

(async () => {
  const { [PENDING_KEY]: pending = [] } = await chrome.storage.local.get(PENDING_KEY);
  if (!pending.length) return;

  let current = [];
  try {
    const raw = JSON.parse(localStorage.getItem(SITE_KEY));
    if (Array.isArray(raw)) current = raw;
  } catch { /* corrupt or empty — treat as no existing uploads */ }

  const known = new Set(current.map((u) => u.id));
  const additions = pending.filter((e) => !known.has(e.id));
  if (!additions.length) return;

  // Newest first, matching how the site orders its own uploads.
  const merged = [...additions, ...current].sort(
    (a, b) => (b.createdAt || 0) - (a.createdAt || 0)
  );

  try {
    localStorage.setItem(SITE_KEY, JSON.stringify(merged));
  } catch { /* storage full/unavailable — non-fatal, retry on next visit */ }
})();
