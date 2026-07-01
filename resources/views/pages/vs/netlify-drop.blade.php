@extends('pages.content')

@section('title', 'html.cloud vs Netlify Drop — send one HTML file privately vs deploy a public site')
@section('description', 'Netlify Drop deploys a folder to a public *.netlify.app URL and asks you to claim it with an account; password protection is a paid Pro feature. html.cloud sends one HTML file through a private, browser-encrypted link with no account. Pricing, a real scenario, and an FAQ.')
@section('og_title', 'html.cloud vs Netlify Drop')
@section('og_description', 'Netlify Drop deploys a public site you claim with an account. html.cloud sends one HTML file privately, encrypted in your browser, no sign-up. Here is how they differ, with pricing.')
@section('canonical', config('app.url') . '/vs/netlify-drop')

@push('head')
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "FAQPage",
  "mainEntity": [
    {
      "@@type": "Question",
      "name": "Can I password-protect a Netlify Drop site for free?",
      "acceptedAnswer": { "@@type": "Answer", "text": "No. Site-wide password protection on Netlify is a paid feature — it lives on the Pro plan, which is 20 USD per month on Netlify's credit-based pricing. A free anonymous Drop is served at a public *.netlify.app URL that anyone who has (or guesses) the address can open. html.cloud is private by default at no cost: the file is encrypted in your browser and the key never reaches the server." }
    },
    {
      "@@type": "Question",
      "name": "Does a Netlify Drop deploy stay up if I do not sign in?",
      "acceptedAnswer": { "@@type": "Answer", "text": "Not reliably. You can drag a folder onto app.netlify.com/drop without an account, but the deploy is anonymous and temporary — Netlify prompts you to claim it by creating an account, and unclaimed deploys are not something you can manage or count on. html.cloud needs no account at all; the link you get is the durable artifact." }
    },
    {
      "@@type": "Question",
      "name": "Should I use Netlify Drop or html.cloud for a single HTML file?",
      "acceptedAnswer": { "@@type": "Answer", "text": "Use Netlify Drop when you want to publish something for the public — a real site, on a custom domain, on a fast CDN. Use html.cloud when you want to hand one self-contained HTML file to a specific person without publishing it: an AI-generated report, a dashboard, a proposal. One is a hosting platform; the other is a private delivery link." }
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
  Both let you get an HTML thing online in seconds without touching a config file — which is why they
  get compared. But they aim at opposite outcomes. <strong>Netlify Drop</strong> <em>publishes</em>: it
  deploys a folder to a public <code>*.netlify.app</code> address on a CDN, and nudges you to claim the
  deploy with an account so you can keep it. <strong>html.cloud</strong> <em>delivers</em>: it takes one
  self-contained HTML file, encrypts it in your browser, and hands you a private link to send to a
  specific person — no account, no public URL.
</p>

@include('partials.vs-screenshot', [
  'src'     => 'screenshots/netlify-drop.webp',
  'alt'     => "Screenshot of the Netlify Drop homepage showing a drag-and-drop zone that reads 'Drag and drop your project folder, zip file, or a single HTML file to deploy instantly' above the heading 'Drag & drop. It's online.'",
  'caption' => "Netlify Drop's homepage: drop a folder, zip, or single HTML file and it's deployed to a public URL you're prompted to claim with an account.",
  'width'   => 1200,
  'height'  => 637,
])

<section class="content-section">
  <h2 class="content-h2">A scenario that tells them apart</h2>
  <p class="content-p">
    Say Claude just built you an interactive sales dashboard as a single HTML file, and you need to get
    it to one client this afternoon. Drop it on Netlify and it lands at a public address like
    <code>silly-name-1a2b3c.netlify.app</code>; anyone who comes across that URL can open it, and to
    keep or replace the deploy you'll be asked to sign up. To make it genuinely private you'd move to
    Netlify's paid tier for password protection. Drop the same file on html.cloud and you get a link
    whose <code class="accent">#</code> fragment carries the decryption key — the client opens it, and
    without that exact link there is nothing to find. Set it to expire in 7 days and it's gone.
  </p>
</section>

<section class="content-section">
  <h2 class="content-h2">At a glance</h2>
  <table class="content-table compare-table">
    <thead>
      <tr><th></th><th class="col-us">html.cloud</th><th>Netlify Drop</th></tr>
    </thead>
    <tbody>
      <tr><td>What you share</td><td class="col-us">One self-contained HTML file</td><td>A whole site / folder of files</td></tr>
      <tr><td>Default visibility</td><td class="col-us">Private — only people with the link</td><td>Public <code>*.netlify.app</code> URL</td></tr>
      <tr><td>Privacy model</td><td class="col-us">Client-side AES-256-GCM; we store only ciphertext</td><td>Files served in plaintext from the CDN</td></tr>
      <tr><td>Password protection</td><td class="col-us">Not needed — the link itself is the secret</td><td>Paid feature (Pro plan, $20/mo)</td></tr>
      <tr><td>Account</td><td class="col-us">None, ever</td><td>Anonymous drops are temporary; keeping one needs an account</td></tr>
      <tr><td>Free-tier ceiling</td><td class="col-us">Free to share; size-limited per file</td><td>~15 GB bandwidth/mo on the free credit allowance</td></tr>
      <tr><td>Expiry &amp; deletion</td><td class="col-us">7 / 30 days / never; replace or delete anytime</td><td>Lives in your account until you delete it</td></tr>
      <tr><td>Custom domain, CDN, CI</td><td class="col-us">No — it isn't a host</td><td>Yes — a full hosting platform</td></tr>
    </tbody>
  </table>
</section>

<section class="content-section">
  <h2 class="content-h2">Where Netlify Drop is the better tool</h2>
  <p class="content-p">
    This isn't a case where one wins outright. If you're putting a <em>public</em> thing online — a
    marketing site, a multi-page prototype, a portfolio — Netlify is built for it and html.cloud simply
    isn't: there's no custom domain, no CDN tuning, no build pipeline, and no multi-file site. html.cloud
    handles exactly one HTML file and nothing else. Reach for Netlify Drop when the goal is <em>publish
    to the world</em>; reach for html.cloud when the goal is <em>send this one file to that one person,
    privately</em>.
  </p>
</section>

<section class="content-section">
  <h2 class="content-h2">FAQ</h2>
  <div class="faq">
    <details class="faq-item">
      <summary>Can I password-protect a Netlify Drop site for free?</summary>
      <p>No — site-wide password protection is a paid Netlify feature (the Pro plan, $20/mo on their
        credit-based pricing). A free anonymous drop sits at a public <code>*.netlify.app</code> URL that
        anyone with the address can open. html.cloud is private at no cost: the file is encrypted in your
        browser and the key stays in the link's <code class="accent">#</code> fragment, never sent to the
        server.</p>
    </details>
    <details class="faq-item">
      <summary>Does a Netlify Drop deploy stay up if I don't sign in?</summary>
      <p>Not reliably. You can drop a folder without an account, but the deploy is anonymous and temporary
        — Netlify prompts you to claim it by creating an account, and there's no dependable way to manage
        an unclaimed one. html.cloud needs no account; the link you get is the durable artifact.</p>
    </details>
    <details class="faq-item">
      <summary>Which should I use for a single HTML file?</summary>
      <p>Netlify Drop if you want to publish it to the public on a real URL or custom domain. html.cloud
        if you want to deliver it privately to specific people without putting it on a public address.</p>
    </details>
  </div>
</section>

@include('partials.compare-links', ['current' => 'vs.netlify'])

<div class="content-cta">
  <a href="{{ route('home') }}" class="content-cta-btn">Go to the start page to share an HTML file →</a>
</div>
@endsection
