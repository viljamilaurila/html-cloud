/**
 * First-run/status screen. The extension can *detect* whether file access is on
 * but cannot grant it — Chrome makes that a manual toggle. So we detect, deep-
 * link to the toggle, and re-check until it flips, gating "ready" on the real
 * check so the extension never looks set up while it silently does nothing.
 */

function refresh() {
  // Callback API; resolves to true once "Allow access to file URLs" is enabled.
  chrome.extension.isAllowedFileSchemeAccess((allowed) => {
    document.body.dataset.state = allowed ? 'ready' : 'pending';
  });
}

document.getElementById('open-details').addEventListener('click', () => {
  // Deep-link straight to this extension's details page, where the toggle lives.
  chrome.tabs.create({ url: `chrome://extensions/?id=${chrome.runtime.id}` });
});

// Re-check on load, when the tab regains focus, and on a slow poll so the page
// advances the moment the user flips the toggle in the other tab.
refresh();
document.addEventListener('visibilitychange', () => { if (!document.hidden) refresh(); });
setInterval(refresh, 1500);
