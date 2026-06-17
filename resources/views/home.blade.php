@extends('layout')
@section('title', 'html.cloud — Private HTML file sharing')
@section('description', 'Share an HTML file with a private link. Encrypted in your browser before upload — only people with your link can read it. Not even us. No sign-up.')
@section('og_title', 'html.cloud — Private HTML file sharing')
@section('og_description', 'Drop an HTML file, get a private link. Encrypted in your browser. Built for AI-generated presentations and sensitive documents.')
@section('canonical', config('app.url'))

@push('head')
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "WebApplication",
  "name": "html.cloud",
  "url": "{{ config('app.url') }}",
  "description": "Private HTML file sharing. Drop an HTML file and get a shareable link — encrypted in your browser with AES-256-GCM before upload. Only people with the link can read the file.",
  "applicationCategory": "Utilities",
  "operatingSystem": "Any",
  "offers": { "@@type": "Offer", "price": "0", "priceCurrency": "USD" }
}
</script>
@endpush

@section('content')
<div class="shell">
  @include('partials.topbar')

  <main class="home-main">
    <h1 class="headline">Share an HTML page in seconds. <em>Private by design.</em></h1>

    <div class="dropzone" id="dropzone">
      <div class="dropzone-icon">
        <svg width="28" height="28" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round">
          <path d="M10 13V4M6 8l4-4 4 4M4 14v2a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1v-2"/>
        </svg>
      </div>
      <div class="dropzone-label">Drop an HTML file here</div>
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

    <p class="explainer-cli">
      or from your terminal: <a href="{{ route('cli') }}"><code>npx html-cloud ./file.html</code></a>
    </p>
    <p class="explainer-cli explainer-cli-alt">
      <a href="{{ route('mcp') }}" class="explainer-cli-claude">or let Claude share for you →</a>
    </p>

    <section class="explainer">
      @include('partials.illustrations.flow')
      <p class="explainer-caption">
        Your browser locks the file before anything leaves your computer.
        The only key is in the link you share — we never see it, so not even we can read your file.
        <a href="{{ route('security') }}" class="explainer-readmore">How the encryption works →</a>
      </p>
    </section>
  </main>

  @include('partials.footer', ['showAbout' => false])
</div>
@endsection

@push('scripts')
@vite('resources/js/upload.js')
@endpush
