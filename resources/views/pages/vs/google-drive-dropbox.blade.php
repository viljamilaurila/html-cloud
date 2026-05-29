@extends('pages.content')

@section('title', 'html.cloud vs Google Drive & Dropbox — zero-knowledge HTML sharing vs cloud storage')
@section('description', 'html.cloud vs Google Drive and Dropbox: cloud storage holds the keys to your files and downloads HTML rather than rendering it; html.cloud encrypts in your browser so even we cannot read it, and shows the page on open. Comparison table, when to use each, and an FAQ.')
@section('og_title', 'html.cloud vs Google Drive & Dropbox')
@section('og_description', 'Drive and Dropbox encrypt at rest but hold the keys, and download HTML instead of showing it. html.cloud is zero-knowledge and renders the page. Here is how they differ.')
@section('canonical', config('app.url') . '/vs/google-drive-dropbox')

@push('head')
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "FAQPage",
  "mainEntity": [
    {
      "@@type": "Question",
      "name": "Are Google Drive and Dropbox zero-knowledge?",
      "acceptedAnswer": { "@@type": "Answer", "text": "No. Both encrypt files at rest and in transit, but they hold the keys, so the provider can technically read your files. html.cloud is zero-knowledge: the file is encrypted in your browser with AES-256-GCM and the key never reaches the server, so not even we can read it." }
    },
    {
      "@@type": "Question",
      "name": "Why not just share an HTML file from Google Drive or Dropbox?",
      "acceptedAnswer": { "@@type": "Answer", "text": "You can, but they treat an HTML file as a download rather than a page to view — recipients typically download it instead of seeing it rendered, and you manage sharing permissions per file. html.cloud shows the page on open and is private by design, with a link that carries the decryption key." }
    },
    {
      "@@type": "Question",
      "name": "Do recipients need an account?",
      "acceptedAnswer": { "@@type": "Answer", "text": "With html.cloud, no — anyone with the link can open the file, and there is no sign-up for you either. Drive and Dropbox links are convenient too, but full access and management assume accounts within their ecosystems." }
    }
  ]
}
</script>
@endpush

@section('page')
<div class="content-eyebrow">Comparison</div>
<h1 class="content-title">html.cloud vs Google Drive &amp; Dropbox</h1>

@include('partials.vs-hero', [
  'glyphs' => ['googledrive', 'dropbox'],
  'links'  => [['label' => 'Google Drive', 'url' => 'https://drive.google.com'],
               ['label' => 'Dropbox', 'url' => 'https://www.dropbox.com']],
])

<p class="content-lead">
  <strong>Google Drive and Dropbox</strong> are general cloud storage: they encrypt your files but
  <em>hold the keys</em>, and they treat an HTML file as a download. <strong>html.cloud</strong> is
  zero-knowledge — encrypted in your browser so even we can't read it — and shows the page when the
  link is opened.
</p>

<section class="content-section">
  <h2 class="content-h2">At a glance</h2>
  <table class="content-table compare-table">
    <thead>
      <tr><th></th><th class="col-us">html.cloud</th><th>Google Drive / Dropbox</th></tr>
    </thead>
    <tbody>
      <tr><td>Privacy model</td><td class="col-us">Zero-knowledge — encrypted in your browser, key never sent</td><td>Encrypted at rest &amp; in transit, but the provider holds the keys</td></tr>
      <tr><td>Can the provider read it?</td><td class="col-us">No — we store only ciphertext</td><td>Technically yes — they manage the keys</td></tr>
      <tr><td>Opening an HTML file</td><td class="col-us">Renders the page in the browser</td><td>Usually downloads the file instead of showing it</td></tr>
      <tr><td>Account</td><td class="col-us">None, for you or the recipient</td><td>Built around accounts in their ecosystem</td></tr>
      <tr><td>Expiry &amp; deletion</td><td class="col-us">7 / 30 days / never; replace or delete anytime</td><td>Manual; expiring links are a paid feature</td></tr>
      <tr><td>Storage, sync, large files</td><td class="col-us">Single HTML file, up to 10&nbsp;MB</td><td>Full storage, folders, versioning, any file type</td></tr>
    </tbody>
  </table>
</section>

<section class="content-section">
  <h2 class="content-h2">When to use which</h2>
  <p class="content-p">
    <strong>Use Google Drive or Dropbox</strong> for what they're built for: storing and syncing lots of
    files, folders and large media, version history, and collaborating inside their ecosystems. They're
    full-featured platforms, and their encryption protects against outside attackers.
  </p>
  <p class="content-p">
    <strong>Use html.cloud</strong> when you want one HTML file to open as a page and stay genuinely
    private — where “the provider could technically read it” isn't good enough. The file is encrypted on
    your device before upload, the link carries the only key, and you can set it to expire or delete it.
  </p>
</section>

<section class="content-section">
  <h2 class="content-h2">FAQ</h2>
  <div class="faq">
    <details class="faq-item">
      <summary>Are Google Drive and Dropbox zero-knowledge?</summary>
      <p>No. Both encrypt files at rest and in transit but hold the keys, so the provider can technically
        read your files. html.cloud encrypts in your browser and the key never reaches the server — not
        even we can read it.</p>
    </details>
    <details class="faq-item">
      <summary>Why not just share an HTML file from Drive or Dropbox?</summary>
      <p>You can, but they treat HTML as a download rather than a page to view, and you manage permissions
        per file. html.cloud shows the rendered page on open and is private by design, with the key carried
        in the link.</p>
    </details>
    <details class="faq-item">
      <summary>Do recipients need an account?</summary>
      <p>With html.cloud, no — anyone with the link can open it, and there's no sign-up for you either.
        Drive and Dropbox links work too, but their full access and management assume accounts.</p>
    </details>
  </div>
</section>

@include('partials.compare-links', ['current' => 'vs.drive'])

<div class="content-cta">
  <a href="{{ route('home') }}" class="content-cta-btn">Go to the start page to share an HTML file →</a>
</div>
@endsection
