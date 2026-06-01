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
    <h1 class="headline">Share an HTML file. <em>Private by design.</em></h1>

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

      <div class="expiry-row">
        <span class="expiry-label">Expires</span>
        <button class="expiry-chip" data-value="7">7 days</button>
        <button class="expiry-chip active" data-value="30">30 days</button>
        <button class="expiry-chip" data-value="never">Never</button>
      </div>

      <div class="dropzone-hover-overlay" aria-hidden="true">Drop to encrypt &amp; share</div>
    </div>

    <div class="uploading-state hidden" id="uploading-state">
      <div class="uploading-spinner"></div>
      <span class="uploading-text">Encrypting &amp; uploading…</span>
    </div>

    <section class="explainer">
      <div class="explainer-label">How it works</div>
      @include('partials.illustrations.flow')
      <div class="explainer-row">
        <!-- Your file -->
        <div class="explainer-card explainer-file">
          <div class="explainer-card-eyebrow">Your file</div>
          <div class="explainer-file-body">
            <svg class="file-icon" width="34" height="42" viewBox="0 0 36 44" fill="none">
              <path d="M3 3a2 2 0 0 1 2-2h18l10 10v30a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V3z" stroke="var(--rule-strong)" stroke-width="1.2" fill="var(--bg)"/>
              <path d="M23 1v8a2 2 0 0 0 2 2h8" stroke="var(--rule-strong)" stroke-width="1.2" fill="none"/>
              <text x="18" y="30" text-anchor="middle" font-family="IBM Plex Mono,monospace" font-size="7.5" fill="var(--accent)" font-weight="500" letter-spacing="0.04em">HTML</text>
            </svg>
            <div>
              <div class="explainer-filename">your-file.html</div>
              <div class="explainer-meta">plaintext</div>
            </div>
          </div>
        </div>

        <svg class="explainer-arrow" width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="var(--ink3)" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><path d="M3 8h10M9 4l4 4-4 4"/></svg>

        <!-- Encrypted bytes -->
        <div class="explainer-card explainer-cipher">
          <div class="explainer-cipher-eyebrow">
            <span>Encrypted bytes</span>
            <span class="cipher-algo">AES-256-GCM</span>
          </div>
          <pre class="cipher-bytes">8a 3f 02 e1 7c 9b 4d a0
4f 21 b8 06 ee 19 7c d3
cd 7e 04 39 a7 b1 0e 5f
60 1f 9c 27 4b ee 8d a3</pre>
          <div class="cipher-fade"></div>
        </div>

        <svg class="explainer-arrow" width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="var(--ink3)" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><path d="M3 8h10M9 4l4 4-4 4"/></svg>

        <!-- The link -->
        <div class="explainer-card explainer-link">
          <div class="explainer-card-eyebrow">The link you share</div>
          <div class="explainer-url">
            <span class="url-base">html.cloud/v/kT4eN7xQ</span><span class="url-hash">#</span><span class="url-key">b3FvXy…</span>
          </div>
          <div class="explainer-footnotes">
            <span>↑ id we store</span>
            <span>↑ <span class="accent">only on your link</span></span>
          </div>
        </div>
      </div>

      <p class="explainer-caption">
        Your browser encrypts the file with <code>AES-256-GCM</code> before anything is uploaded.
        The key sits after the <code class="accent">#</code> in the link — browsers never send that part to servers.
        We store the encrypted blob; only people you give the link to can read it.
        <a href="{{ route('security') }}" class="explainer-readmore">Read how the encryption works →</a>
      </p>
    </section>
  </main>

  @include('partials.footer', ['showAbout' => false])
</div>
@endsection

@push('scripts')
@vite('resources/js/upload.js')
@endpush
