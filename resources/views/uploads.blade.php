@extends('pages.content')
@section('title', 'Uploaded from this device — html.cloud')
@section('robots', 'noindex, nofollow')

@section('page')
<section class="uploads-page">
  <h1 class="uploads-title">Uploaded from this device</h1>
  <p class="uploads-note">
    These are files you uploaded in <strong>this browser</strong>. This list — and your ability
    to manage or delete them — lives only on this device, never on our servers. Clear your browser
    data or switch devices and it’s gone, and we can’t recover it. Keep the links somewhere safe if
    they matter.
  </p>

  <div id="uploads-list" class="uploads-list"></div>
  <p id="uploads-empty" class="uploads-empty hidden">Nothing uploaded from this device yet.</p>

  <div class="uploads-add">
    <div class="uploads-add-label">Upload a file</div>
    <div class="dropzone dropzone-secondary" id="dropzone">
      <div class="dropzone-icon">
        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round">
          <path d="M10 13V4M6 8l4-4 4 4M4 14v2a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1v-2"/>
        </svg>
      </div>
      <div class="dropzone-label dropzone-label-sm">Drop an HTML file to share</div>
      <div class="dropzone-sub">
        or <label for="file-input" class="file-link">choose from your computer</label>
        <input type="file" id="file-input" accept=".html,.htm" style="display:none">
      </div>
      <div class="dropzone-hover-overlay" aria-hidden="true">Drop to encrypt &amp; share</div>
    </div>

    <label class="upload-option" id="upload-options">
      <input type="checkbox" id="sensitive-toggle">
      <span class="upload-option-text">
        Extra-private link
        <span class="upload-option-hint">Hides the key from the address bar so it can’t be seen on screen-shares. Share only with the Copy button.</span>
      </span>
    </label>

    <div class="uploading-state hidden" id="uploading-state">
      <div class="uploading-spinner"></div>
      <span class="uploading-text">Encrypting &amp; uploading…</span>
    </div>
  </div>
</section>
@endsection

@push('scripts')
@vite('resources/js/upload.js')
@vite('resources/js/uploads-page.js')
@endpush
