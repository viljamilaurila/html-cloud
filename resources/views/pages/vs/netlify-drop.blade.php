@extends('pages.content')

@section('title', 'html.cloud vs Netlify Drop — private single-file sharing vs public deploys')
@section('description', 'html.cloud vs Netlify Drop: Netlify Drop publishes a whole site to a public URL on its CDN; html.cloud shares one HTML file through a private, client-side-encrypted link with no account. When to use each, a feature comparison, and an FAQ.')
@section('og_title', 'html.cloud vs Netlify Drop')
@section('og_description', 'Netlify Drop deploys a public site. html.cloud shares one HTML file privately, encrypted in your browser, with no account. Here is how they differ.')
@section('canonical', config('app.url') . '/vs/netlify-drop')

@push('head')
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "FAQPage",
  "mainEntity": [
    {
      "@@type": "Question",
      "name": "Is html.cloud a replacement for Netlify Drop?",
      "acceptedAnswer": { "@@type": "Answer", "text": "Not exactly — they solve different problems. Netlify Drop deploys a whole site or folder to a public URL on a CDN. html.cloud shares a single self-contained HTML file through a private, encrypted link. Use Netlify Drop to host a public site; use html.cloud to send one file privately." }
    },
    {
      "@@type": "Question",
      "name": "Is a Netlify Drop site private?",
      "acceptedAnswer": { "@@type": "Answer", "text": "By default a Netlify deploy is served at a public URL that anyone can open. Password protection is a paid feature. html.cloud is private by design: the file is encrypted in your browser and the decryption key lives in the link's URL fragment, which is never sent to the server." }
    },
    {
      "@@type": "Question",
      "name": "Do I need an account?",
      "acceptedAnswer": { "@@type": "Answer", "text": "html.cloud needs no account at all. Netlify Drop lets you drop a site without signing in, but the unclaimed deploy is temporary — you must create an account to keep it." }
    }
  ]
}
</script>
@endpush

@section('page')
<div class="content-eyebrow">Comparison</div>
<h1 class="content-title">html.cloud vs Netlify Drop</h1>

@include('partials.vs-hero', [
  'glyphs' => ['netlify'],
  'links'  => [['label' => 'Netlify Drop', 'url' => 'https://app.netlify.com/drop']],
])

<p class="content-lead">
  <strong>Netlify Drop</strong> deploys a whole site to a <em>public</em> URL on a CDN.
  <strong>html.cloud</strong> shares a single HTML file through a <em>private</em> link, encrypted
  in your browser, with no account. Pick Netlify Drop to publish a site; pick html.cloud to send
  one file to someone privately.
</p>

<section class="content-section">
  <h2 class="content-h2">At a glance</h2>
  <table class="content-table compare-table">
    <thead>
      <tr><th></th><th class="col-us">html.cloud</th><th>Netlify Drop</th></tr>
    </thead>
    <tbody>
      <tr><td>What you share</td><td class="col-us">One self-contained HTML file</td><td>A whole site / folder of files</td></tr>
      <tr><td>Default visibility</td><td class="col-us">Private — only people with the link</td><td>Public URL anyone can open</td></tr>
      <tr><td>Privacy model</td><td class="col-us">Client-side AES-256-GCM; we store only ciphertext</td><td>Files stored and served in plaintext by the host</td></tr>
      <tr><td>Account</td><td class="col-us">None</td><td>Unclaimed drops are temporary; keeping one needs an account</td></tr>
      <tr><td>Expiry &amp; deletion</td><td class="col-us">7 / 30 days / never; replace or delete anytime</td><td>Lives until you delete it from your account</td></tr>
      <tr><td>Custom domain, CDN, CI</td><td class="col-us">No — not a host</td><td>Yes — a full hosting platform</td></tr>
    </tbody>
  </table>
</section>

<section class="content-section">
  <h2 class="content-h2">When to use which</h2>
  <p class="content-p">
    <strong>Use Netlify Drop</strong> when you want to <em>publish</em> — a real, public website or
    multi-file prototype, on a fast CDN, optionally with a custom domain and continuous deploys.
    It's an excellent free way to put a site online.
  </p>
  <p class="content-p">
    <strong>Use html.cloud</strong> when you want to <em>privately deliver</em> a single HTML file —
    an AI-generated report, a presentation, a dashboard, a proposal — to a specific person, without
    putting it on a public URL or asking anyone to sign in. The file is encrypted before it leaves
    your device, the link carries the key, and you can set it to expire.
  </p>
</section>

<section class="content-section">
  <h2 class="content-h2">FAQ</h2>
  <div class="faq">
    <details class="faq-item">
      <summary>Is html.cloud a replacement for Netlify Drop?</summary>
      <p>Not exactly — they solve different problems. Netlify Drop deploys a whole site to a public URL;
        html.cloud shares one file through a private, encrypted link. Use Netlify to host a site, html.cloud
        to send a file privately.</p>
    </details>
    <details class="faq-item">
      <summary>Is a Netlify Drop site private?</summary>
      <p>By default it's served at a public URL anyone can open; password protection is a paid feature.
        html.cloud is private by design — the file is encrypted in your browser and the key stays in the
        link's <code>#</code> fragment, never sent to the server.</p>
    </details>
    <details class="faq-item">
      <summary>Do I need an account?</summary>
      <p>html.cloud needs none. Netlify Drop lets you drop a site without signing in, but that deploy is
        temporary unless you create an account to claim it.</p>
    </details>
  </div>
</section>

@include('partials.compare-links', ['current' => 'vs.netlify'])

<div class="content-cta">
  <a href="{{ route('home') }}" class="content-cta-btn">Go to the start page to share an HTML file →</a>
</div>
@endsection
