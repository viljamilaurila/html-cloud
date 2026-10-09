@extends('layout')
@section('title', $slugTitle ? $slugTitle . ' — html.cloud' : 'html.cloud')
@section('robots', 'noindex, nofollow')
@section('og_title', $slugTitle ?: 'An encrypted file shared via html.cloud')
@section('og_description', 'Open it with the complete link — including the part after the “#”. That part is the decryption key; without it the file can’t be unlocked, and not even html.cloud can recover it.')

@section('content')
<div id="loading-screen" class="loading-screen">
  <div class="loading-inner">
    <div class="loading-spinner"></div>
    <span class="loading-text">Decrypting…</span>
  </div>
</div>

<div id="error-screen" class="error-screen hidden">
  <div class="error-inner">
    <div class="error-icon">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></svg>
    </div>
    <h2 class="error-title" id="error-title">Unable to decrypt</h2>
    <p class="error-body" id="error-body">The link may be incomplete or the file has been removed.</p>

    <div id="missing-key-help" class="missing-key-help hidden">
      <div class="mk-url-display">
        <span class="mk-base" id="mk-url-full"></span><span class="mk-key-group"><span class="mk-key">#a8Kf3v…</span><span class="mk-pointer"><svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M8 13V4M4 8l4-4 4 4"/></svg>the part you’re missing</span></span>
      </div>
      <p class="mk-caption">Ask whoever shared the file to send you the complete link.</p>
    </div>

    <a href="/" class="error-link">← Back to html.cloud</a>
  </div>
</div>

<iframe id="content-frame" class="content-frame hidden" sandbox="allow-scripts allow-popups allow-popups-to-escape-sandbox" title="Shared HTML file"></iframe>

<div id="upload-toast" class="upload-toast hidden" role="status">
  <div class="upload-toast-body">
    <svg class="upload-toast-check" width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 8.5l3.2 3L13 4.5"/></svg>
    <div class="upload-toast-text">
      <strong id="upload-toast-title">Encrypted &amp; uploaded</strong>
      <span id="upload-toast-sub">Your link is ready to share.</span>
    </div>
    <button type="button" class="upload-toast-copy" id="upload-toast-copy">Copy link</button>
    <button type="button" class="upload-toast-x" id="upload-toast-dismiss" aria-label="Dismiss">×</button>
  </div>
</div>

{{-- Owner only (this browser uploaded the document): drop a newer file anywhere
     on the page to replace it behind the same link. --}}
<div id="replace-drop" class="replace-drop hidden" aria-hidden="true">
  <div class="replace-drop-inner">
    <span class="replace-drop-title">Drop to replace this page</span>
    <span class="replace-drop-sub">The link stays the same.</span>
  </div>
</div>

<div id="replace-confirm" class="replace-confirm hidden" role="alertdialog" aria-modal="true" aria-labelledby="replace-confirm-title" aria-describedby="replace-confirm-text">
  <div class="replace-confirm-card">
    <h2 class="replace-confirm-title" id="replace-confirm-title">Replace this page?</h2>
    <p class="replace-confirm-file" id="replace-confirm-name"></p>
    <p class="replace-confirm-text" id="replace-confirm-text">
      The new file takes this page’s place behind the same link. Everyone who has
      the link sees the new version — the current one is gone for good.
    </p>
    <div class="replace-confirm-actions">
      <button type="button" class="link-btn link-btn-ghost" id="replace-confirm-cancel">Cancel</button>
      <button type="button" class="link-btn link-btn-accent" id="replace-confirm-ok">Replace page</button>
    </div>
  </div>
</div>

<div id="hc-badge" class="hc-badge hidden">
  <div class="hc-badge-inner">
    <button type="button" class="hc-badge-lock" aria-label="Encrypted — shared via html.cloud" aria-expanded="false">
      <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="7" width="10" height="8" rx="1.5"/><path d="M5 7V5a3 3 0 0 1 6 0v2"/></svg>
    </button>
    <div class="hc-badge-body">
      <span class="hc-badge-txt">Encrypted &middot; <a href="https://html.cloud" target="_blank" rel="noopener">html.cloud</a></span>
      <span class="hc-badge-sep"></span>
      <button type="button" class="hc-badge-copy">
        <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="5" width="9" height="9" rx="1.5"/><path d="M11 5V3.5A1.5 1.5 0 0 0 9.5 2H3.5A1.5 1.5 0 0 0 2 3.5v6A1.5 1.5 0 0 0 3.5 11H5"/></svg>
        <span class="hc-badge-copy-label">Copy link</span>
      </button>
      <button type="button" class="hc-badge-download">
        <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 2v7.5M4.75 6.5L8 9.75l3.25-3.25M2.5 12.5h11"/></svg>
        <span class="hc-badge-download-label">Download</span>
      </button>
      <button type="button" class="hc-badge-replace hidden">
        <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13.5 6.5A5.5 5.5 0 0 0 3.4 4.6M2.5 9.5a5.5 5.5 0 0 0 10.1 1.9"/><path d="M3 2v3h3M13 14v-3h-3"/></svg>
        <span class="hc-badge-replace-label">Replace file</span>
      </button>
      <input type="file" class="hc-badge-replace-input" accept=".html,.htm" hidden>
      <a class="hc-badge-manage hidden" href="{{ route('uploads') }}">
        <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="12" height="3.5" rx="1"/><rect x="2" y="9" width="12" height="3.5" rx="1"/></svg>
        Your uploads
      </a>
    </div>
  </div>
</div>

<script>
  window.__DOC_ID__ = @json($doc->id);
</script>
@endsection

@push('scripts')
@vite('resources/js/viewer.js')
@endpush
