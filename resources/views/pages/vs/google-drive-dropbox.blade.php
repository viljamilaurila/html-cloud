@extends('pages.content')

@section('title', 'html.cloud vs Google Drive & Dropbox — zero-knowledge HTML sharing vs cloud storage')
@section('description', 'Share an HTML file from Google Drive or Dropbox and it downloads instead of rendering, the provider holds the keys, and expiring/password links are paid features. html.cloud encrypts in your browser so not even we can read it, and opens the page on click. A real scenario, the tradeoffs, and an FAQ.')
@section('og_title', 'html.cloud vs Google Drive & Dropbox')
@section('og_description', 'Drive and Dropbox hold the keys and download HTML instead of showing it; expiring links are paid. html.cloud is zero-knowledge and renders the page. Here is how they differ.')
@section('canonical', config('app.url') . '/vs/google-drive-dropbox')

@push('head')
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "FAQPage",
  "mainEntity": [
    {
      "@@type": "Question",
      "name": "Will an HTML file shared from Google Drive or Dropbox open as a web page?",
      "acceptedAnswer": { "@@type": "Answer", "text": "No. Both treat an .html file as a document to preview or download, not a site to run — the recipient sees a download prompt or a raw preview, and scripts and styles do not execute the way they would in a browser. html.cloud serves the real HTML file, so it renders as the finished page when the link is opened." }
    },
    {
      "@@type": "Question",
      "name": "Are Google Drive and Dropbox zero-knowledge?",
      "acceptedAnswer": { "@@type": "Answer", "text": "No. Both encrypt files at rest and in transit, but they hold the keys, so the provider can technically read your files and hand them over under legal process. html.cloud is zero-knowledge: the file is encrypted in your browser with AES-256-GCM and the key never reaches the server, so not even we can read it." }
    },
    {
      "@@type": "Question",
      "name": "Can I make a shared link expire?",
      "acceptedAnswer": { "@@type": "Answer", "text": "On Drive and Dropbox, link expiry and password protection are paid features (Dropbox reserves them for its paid plans, and Drive expiry needs a Workspace subscription). html.cloud lets you set a 7- or 30-day expiry, or delete the file, at no cost." }
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
  <strong>Google Drive and Dropbox</strong> are general cloud storage — brilliant for keeping and syncing
  files, but they encrypt with keys <em>they</em> hold, and they treat an HTML file as something to
  download rather than a page to open. <strong>html.cloud</strong> does the narrow thing they don't: it's
  zero-knowledge — encrypted in your browser so even we can't read it — and it renders the page the moment
  the link is opened.
</p>

<section class="content-section">
  <h2 class="content-h2">The thing that trips people up</h2>
  <p class="content-p">
    Drive and Dropbox are so good at holding files that people assume they're also good at
    <em>presenting</em> one — but an HTML file is a small website, not a document, and neither is built to
    run it. Share a Drive link to an <code>.html</code> file and the recipient usually gets a download
    prompt or a stripped preview where the scripts and styles don't fire; the interactive report you built
    arrives broken or as a file they have to save and open by hand. html.cloud serves the real file over
    the web, so a click opens the finished page exactly as intended — and because it's encrypted on your
    device first, the server only ever holds ciphertext.
  </p>
</section>

<section class="content-section">
  <h2 class="content-h2">At a glance</h2>
  <table class="content-table compare-table">
    <thead>
      <tr><th></th><th class="col-us">html.cloud</th><th>Google Drive / Dropbox</th></tr>
    </thead>
    <tbody>
      <tr><td>Opening an HTML file</td><td class="col-us">Renders the page in the browser</td><td>Downloads it or shows a stripped preview</td></tr>
      <tr><td>Privacy model</td><td class="col-us">Zero-knowledge — encrypted in your browser, key never sent</td><td>Encrypted at rest &amp; in transit, but the provider holds the keys</td></tr>
      <tr><td>Can the provider read it?</td><td class="col-us">No — we store only ciphertext</td><td>Technically yes — they manage the keys</td></tr>
      <tr><td>Expiring / password links</td><td class="col-us">7 / 30 days / never, free</td><td>Paid feature on both</td></tr>
      <tr><td>Account</td><td class="col-us">None, for you or the recipient</td><td>Built around accounts in their ecosystem</td></tr>
      <tr><td>Storage, sync, large files</td><td class="col-us">Single HTML file, up to 10&nbsp;MB</td><td>Full storage, folders, versioning, any file type</td></tr>
    </tbody>
  </table>
</section>

<section class="content-section">
  <h2 class="content-h2">Where Drive and Dropbox are the better tool</h2>
  <p class="content-p">
    For almost everything storage-shaped, they win outright and html.cloud can't compete: syncing folders
    across devices, keeping large media and documents of every type, version history, real-time
    collaboration in Docs, shared team drives. Their encryption genuinely protects your files from outside
    attackers, which is enough for most content. Reach for html.cloud only in the specific case they
    handle poorly: one HTML file that should open as a page and stay <em>genuinely</em> private — where
    "the provider could technically read it" isn't acceptable, and you want a link you can expire without
    paying for a plan.
  </p>
</section>

<section class="content-section">
  <h2 class="content-h2">FAQ</h2>
  <div class="faq">
    <details class="faq-item">
      <summary>Will an HTML file shared from Drive or Dropbox open as a web page?</summary>
      <p>No — both treat <code>.html</code> as a file to download or preview, not a site to run, so scripts
        and styles don't execute as they would in a browser. html.cloud serves the real file, so it renders
        as the finished page when the link is opened.</p>
    </details>
    <details class="faq-item">
      <summary>Are Google Drive and Dropbox zero-knowledge?</summary>
      <p>No. Both encrypt at rest and in transit but hold the keys, so the provider can technically read
        your files. html.cloud encrypts in your browser and the key never reaches the server — not even we
        can read it.</p>
    </details>
    <details class="faq-item">
      <summary>Can I make a shared link expire?</summary>
      <p>On Drive and Dropbox, link expiry and password protection are paid features. html.cloud lets you
        set a 7- or 30-day expiry, or delete the file, at no cost.</p>
    </details>
  </div>
</section>

@include('partials.compare-links', ['current' => 'vs.drive'])

<div class="content-cta">
  <a href="{{ route('home') }}" class="content-cta-btn">Go to the start page to share an HTML file →</a>
</div>
@endsection
