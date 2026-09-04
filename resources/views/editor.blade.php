@extends('layout')
@section('title', 'Manage — html.cloud')
@section('robots', 'noindex, nofollow')

@section('content')
<div class="shell">
  <header class="topbar">
    <a href="/" class="wordmark" aria-label="html.cloud"></a>
    <span class="topbar-tagline">encrypted in your browser</span>
  </header>

  <div id="auth-error" class="auth-error hidden">
    <div class="error-icon">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></svg>
    </div>
    <h2>Invalid edit link</h2>
    <p>This link doesn't match the document. Make sure you're using the full URL, including the part after <code>#</code>.</p>
    <a href="/" class="error-link">← Back to html.cloud</a>
  </div>

  <main class="editor-main" id="editor-ui">

    <div class="done-badge" id="editor-badge">
      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
      Your document
    </div>

    <!-- Share link -->
    <div class="share-card" id="share-card">
      <div class="share-card-label">Share link</div>
      <div class="share-url" id="share-url">
        <span class="share-url-loading">Decrypting link…</span>
      </div>
      <div class="link-actions" id="share-actions" style="opacity:0;pointer-events:none">
        <a class="link-btn link-btn-primary" id="open-share" href="#" target="_blank" rel="noopener noreferrer">
          Open
          <svg width="11" height="11" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3H3v10h10v-3M9 3h4v4M13 3 7 9"/></svg>
        </a>
        <button class="link-btn link-btn-ghost" id="copy-share">
          <svg width="12" height="12" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="5" width="9" height="9" rx="1.5"/><path d="M3 11V3.5A1.5 1.5 0 0 1 4.5 2H11"/></svg>
          Copy
        </button>
      </div>
      <p class="share-card-hint" id="share-card-hint" style="opacity:0">Share with <strong>Copy</strong> or <strong>Open</strong>. The key after the <code>#</code> is what unlocks the file — keep the link complete.</p>
      <div class="expiry-info-row" id="expiry-row" style="opacity:0">
        <span id="expiry-text"></span>
        <div class="expiry-chips">
          <button class="expiry-chip-sm" data-value="7"></button>
          <button class="expiry-chip-sm" data-value="30"></button>
          <button class="expiry-chip-sm" data-value="never">Never</button>
        </div>
      </div>
      <label class="sensitive-row" id="sensitive-row" style="opacity:0">
        <input type="checkbox" id="sensitive-toggle">
        <span class="sensitive-row-text">
          Extra-private link
          <span class="sensitive-row-hint">Hides the key from the address bar so it can’t be seen on screen-shares. Share only with Copy/Open.</span>
        </span>
      </label>
    </div>

    <!-- Replace file -->
    <div class="editor-section-label">Replace file</div>
    <div class="dropzone dropzone-sm" id="dropzone">
      <div class="dropzone-icon">
        <svg width="22" height="22" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round">
          <path d="M10 13V4M6 8l4-4 4 4M4 14v2a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1v-2"/>
        </svg>
      </div>
      <div class="dropzone-label dropzone-label-sm">Drop an HTML file to replace</div>
      <div class="dropzone-sub">
        or <label for="file-input" class="file-link">choose from your computer</label>
        <input type="file" id="file-input" accept=".html,.htm" style="display:none">
      </div>
      <div class="dropzone-hover-overlay" aria-hidden="true">Drop to re-encrypt &amp; replace</div>
    </div>

    <div class="uploading-state hidden" id="uploading-state">
      <div class="uploading-spinner"></div>
      <span class="uploading-text">Re-encrypting &amp; uploading…</span>
    </div>

    <div class="update-success hidden" id="update-success">
      <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 8.5l3.2 3L13 4.5"/></svg>
      File replaced. The share link still works.
    </div>

    <!-- Management link -->
    <div class="edit-card" id="manage-card" style="opacity:0">
      <div class="edit-card-label">Your management link</div>
      <div class="edit-url" id="manage-url"></div>
      <div class="link-actions-sm">
        <button class="link-btn link-btn-ghost-sm" id="copy-manage">
          <svg width="12" height="12" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="5" width="9" height="9" rx="1.5"/><path d="M3 11V3.5A1.5 1.5 0 0 1 4.5 2H11"/></svg>
          Copy management link
        </button>
      </div>
      <p class="edit-hint"><strong>Bookmark this page now — it's your only way back.</strong> There's no account, so this link is the one thing that lets you replace or delete the document later. It stays in your browser history. Don't share it with viewers.</p>
    </div>

    <!-- Delete -->
    <div class="editor-danger-row">
      <button class="danger-btn" id="delete-btn">Delete this document</button>
    </div>

  </main>
</div>

<script nonce="{{ Vite::cspNonce() }}">
  window.__DOC_ID__ = @json($doc->id);
  window.__EXPIRES_AT__ = @json($doc->expires_at?->toIso8601String());
</script>
@endsection

@push('scripts')
@vite('resources/js/editor.js')
@endpush
