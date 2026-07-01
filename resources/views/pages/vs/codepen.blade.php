@extends('pages.content')

@section('title', 'html.cloud vs CodePen — send a finished HTML file privately vs a public code playground')
@section('description', 'CodePen is a front-end playground where Pens are public by default and private Pens need a PRO plan (from $8/mo); it shows the recipient an editor and source. html.cloud sends one finished HTML file as a private, browser-encrypted link that opens as the rendered page — no account. Pricing, a real scenario, and an FAQ.')
@section('og_title', 'html.cloud vs CodePen')
@section('og_description', 'CodePen shows your source in a public editor; private Pens are a paid feature. html.cloud delivers a finished HTML file privately as a rendered page, encrypted, no account. Here is how they differ.')
@section('canonical', config('app.url') . '/vs/codepen')

@push('head')
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "FAQPage",
  "mainEntity": [
    {
      "@@type": "Question",
      "name": "Are CodePen Pens private, and does it cost anything?",
      "acceptedAnswer": { "@@type": "Answer", "text": "On CodePen's free tier, Pens are public by default and listed on your profile and in search. Private Pens are a PRO feature — any paid plan unlocks them, starting at 8 USD per month (the Starter plan). html.cloud is private at no cost: the file is encrypted in your browser and the key lives in the link's fragment, which never reaches the server." }
    },
    {
      "@@type": "Question",
      "name": "Does the recipient see my source code or the finished page?",
      "acceptedAnswer": { "@@type": "Answer", "text": "CodePen is built to show source — the recipient lands in an editor with your HTML, CSS, and JS visible alongside a preview. html.cloud shows only the rendered page; the recipient opens the link and sees the finished result, not the code behind it." }
    },
    {
      "@@type": "Question",
      "name": "What is the difference between html.cloud and CodePen?",
      "acceptedAnswer": { "@@type": "Answer", "text": "CodePen is a social front-end playground for writing, showing, and iterating code, with a community and live editing. html.cloud does one thing: privately deliver a finished, self-contained HTML file to a specific person through an encrypted link, with no account and nothing public." }
    }
  ]
}
</script>
@endpush

@section('page')
<div class="content-eyebrow">Comparison</div>
<h1 class="content-title">html.cloud vs CodePen</h1>

@include('partials.vs-hero', [
  'glyphs' => ['codepen'],
  'links'  => [['label' => 'CodePen', 'url' => 'https://codepen.io']],
])

<p class="content-lead">
  Both can turn HTML into something you send with a link, which is why they come up together — but they
  point in opposite directions. <strong>CodePen</strong> is a playground for the <em>code</em>: it puts
  the recipient in an editor, shows the source, and lists your work publicly unless you pay for privacy.
  <strong>html.cloud</strong> is for the <em>result</em>: it takes one finished HTML file, encrypts it in
  your browser, and gives you a private link that opens the rendered page — no editor, no account,
  nothing listed.
</p>

@include('partials.vs-screenshot', [
  'src'     => 'screenshots/codepen.webp',
  'alt'     => "Screenshot of the CodePen homepage with the headline 'The best place to build, test, and discover front-end code' and a live HTML, SCSS, and JS code editor preview.",
  'caption' => "CodePen's homepage — a social front-end editor for building and showing code, with Pens public by default unless you upgrade to PRO.",
  'width'   => 1200,
  'height'  => 744,
])

<section class="content-section">
  <h2 class="content-h2">A scenario that tells them apart</h2>
  <p class="content-p">
    Suppose an AI just generated a polished HTML dashboard and you want your manager to look at it. Put
    it in a Pen and, on the free tier, it's a public URL on your CodePen profile and in search, opened in
    an editor where your manager sees the raw code first and the result second — to keep it off the public
    web you'd upgrade to PRO. Drop the same file on html.cloud and the link opens straight to the
    dashboard, private to whoever you send it to, with the decryption key living in the link's
    <code class="accent">#</code> fragment and never touching the server. One is for showing how it's
    built; the other is for handing over the thing itself.
  </p>
</section>

<section class="content-section">
  <h2 class="content-h2">At a glance</h2>
  <table class="content-table compare-table">
    <thead>
      <tr><th></th><th class="col-us">html.cloud</th><th>CodePen</th></tr>
    </thead>
    <tbody>
      <tr><td>What it's for</td><td class="col-us">Privately delivering a finished HTML file</td><td>Writing, showing &amp; iterating front-end code</td></tr>
      <tr><td>What the recipient sees</td><td class="col-us">The rendered page</td><td>An editor with your source code + preview</td></tr>
      <tr><td>Default visibility</td><td class="col-us">Private — only people with the link</td><td>Public &amp; listed on your profile / in search</td></tr>
      <tr><td>Privacy model</td><td class="col-us">Client-side AES-256-GCM; we store only ciphertext</td><td>Code stored and shown in plaintext</td></tr>
      <tr><td>Cost of privacy</td><td class="col-us">Free — the link is the secret</td><td>Private Pens need PRO (from $8/mo)</td></tr>
      <tr><td>Account</td><td class="col-us">None</td><td>Account needed to save and manage Pens</td></tr>
      <tr><td>Live editing / community</td><td class="col-us">No — replace the file via a private edit link</td><td>Yes — in-browser editing, embeds, a community</td></tr>
    </tbody>
  </table>
</section>

<section class="content-section">
  <h2 class="content-h2">Where CodePen is the better tool</h2>
  <p class="content-p">
    If the <em>code</em> is the point, CodePen wins and html.cloud isn't even in the running. Prototyping
    in the browser, sharing a CSS technique, embedding a live editable demo in an article, teaching, pair
    programming, browsing what others have built — that's what CodePen is for, and it's excellent at it.
    html.cloud has no editor, no embeds, and no community; it can't show or teach code. Reach for it only
    at the end of that journey, when you have a finished HTML file and need to hand it to a specific
    person without publishing it or showing them the source.
  </p>
</section>

<section class="content-section">
  <h2 class="content-h2">FAQ</h2>
  <div class="faq">
    <details class="faq-item">
      <summary>Are CodePen Pens private, and does it cost anything?</summary>
      <p>On the free tier, Pens are public by default and appear on your profile and in search. Private
        Pens are a PRO feature — any paid plan unlocks them, from $8/mo (Starter). html.cloud is private at
        no cost: encrypted in your browser, with the key in the link's <code class="accent">#</code>
        fragment, never sent to the server.</p>
    </details>
    <details class="faq-item">
      <summary>Does the recipient see my source or the finished page?</summary>
      <p>CodePen shows source — the recipient lands in an editor with your code visible next to a preview.
        html.cloud shows only the rendered page; they open the link and see the finished result.</p>
    </details>
    <details class="faq-item">
      <summary>What's the difference between html.cloud and CodePen?</summary>
      <p>CodePen is a social playground for writing and showing code, with a community and live editing.
        html.cloud does one thing: privately deliver a finished HTML file to a specific person via an
        encrypted link, with no account and nothing public.</p>
    </details>
  </div>
</section>

@include('partials.compare-links', ['current' => 'vs.codepen'])

<div class="content-cta">
  <a href="{{ route('home') }}" class="content-cta-btn">Go to the start page to share an HTML file →</a>
</div>
@endsection
