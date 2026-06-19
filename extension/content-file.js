/**
 * Injected onto local HTML files (file:///*.html). Drops a floating "Share"
 * button onto the page; one click reads the file, hands the bytes to the
 * background worker to encrypt + upload, and copies the share link.
 *
 * Runs in an isolated world but shares the page DOM, so the button lives inside
 * a Shadow root to stay immune to the page's own styles.
 */

(() => {
  // Only the top document — never inside iframes the HTML file may embed.
  if (window.top !== window) return;
  // Belt-and-suspenders: the manifest match already limits us to .html/.htm.
  if (!/\.html?$/i.test(location.pathname)) return;

  const host = document.createElement('div');
  host.id = 'hc-share-host';
  // Top-right, under Chrome's extensions menu — where the user's attention is.
  host.style.cssText = 'all:initial;position:fixed;z-index:2147483647;top:16px;right:16px;';
  const root = host.attachShadow({ mode: 'closed' });
  root.innerHTML = `
    <style>
      .pill{display:inline-flex;align-items:center;gap:8px;font:500 13px/1
        -apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;color:#1a1a1a;
        background:#fff;border:1px solid rgba(0,0,0,.12);border-radius:999px;
        padding:10px 16px;box-shadow:0 4px 16px rgba(0,0,0,.12);cursor:pointer;
        transition:transform .12s ease,box-shadow .12s ease;user-select:none;}
      .pill:hover{transform:translateY(-1px);box-shadow:0 6px 20px rgba(0,0,0,.16);}
      .pill[disabled]{opacity:.7;cursor:default;transform:none;}
      .pill.done{color:#1f7a3d;border-color:rgba(31,122,61,.3);}
      .pill.err{color:#8a3b1f;border-color:rgba(138,59,31,.3);}
      svg{width:14px;height:14px;flex:0 0 auto;}
      a{color:inherit;text-decoration:underline;}
    </style>
    <button class="pill" part="pill">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
        stroke-linecap="round" stroke-linejoin="round">
        <rect x="3" y="11" width="18" height="11" rx="2"/>
        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
      </svg>
      <span class="label">Share to html.cloud</span>
    </button>`;
  document.documentElement.appendChild(host);

  const pill = root.querySelector('.pill');
  const label = root.querySelector('.label');

  pill.addEventListener('click', async () => {
    if (pill.disabled) return;
    pill.disabled = true;
    pill.classList.remove('done', 'err');
    label.textContent = 'Encrypting…';

    try {
      const bytes = await readFileBytes();

      const [{ b64url }, { MAX_SIZE }] = await Promise.all([
        import(chrome.runtime.getURL('vendor/crypto.js')),
        import(chrome.runtime.getURL('vendor/share-core.js')),
      ]);
      if (bytes.length > MAX_SIZE) throw new Error('File is larger than 10 MB.');

      const name = decodeURIComponent(location.pathname.split('/').pop()) || 'page.html';
      const resp = await chrome.runtime.sendMessage({
        type: 'hc-share',
        name,
        data: b64url(bytes),
      });
      if (!resp?.ok) throw new Error(resp?.error || 'Upload failed');

      let copied = false;
      try { await navigator.clipboard.writeText(resp.shareUrl); copied = true; } catch { /* no gesture/permission */ }

      pill.classList.add('done');
      // The doc opened in a new tab (background); offer Copy/Open here for re-use.
      const note = resp.opened
        ? (copied ? 'Shared ✓ · opened &amp; copied' : 'Shared ✓ · opened in a new tab')
        : (copied ? 'Shared ✓ · link copied' : 'Shared ✓');
      label.innerHTML =
        `${note} · <a href="${resp.shareUrl}" target="_blank" rel="noopener">open</a>`;
    } catch (err) {
      pill.classList.add('err');
      label.textContent = err?.message || 'Share failed';
      // Let the user try again after an error.
      setTimeout(() => {
        pill.classList.remove('err');
        pill.disabled = false;
        label.textContent = 'Share to html.cloud';
      }, 4000);
    } finally {
      if (pill.classList.contains('done')) pill.disabled = true;
    }
  });

  /**
   * Read the file's raw bytes. With file access granted, fetching the page's own
   * file:// URL gives a byte-identical copy; if that's blocked we fall back to
   * re-serializing the live DOM (loses only the original byte layout).
   */
  async function readFileBytes() {
    try {
      const buf = await (await fetch(location.href)).arrayBuffer();
      return new Uint8Array(buf);
    } catch {
      const html = '<!DOCTYPE html>\n' + document.documentElement.outerHTML;
      return new TextEncoder().encode(html);
    }
  }
})();
