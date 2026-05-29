@extends('pages.content')

@section('title', 'html.cloud vs CodePen — private file delivery vs public code show-and-tell')
@section('description', 'html.cloud vs CodePen: CodePen is a public front-end editor for sharing and iterating code; html.cloud privately delivers a finished HTML file through a client-side-encrypted link with no account. Comparison table, when to use each, and an FAQ.')
@section('og_title', 'html.cloud vs CodePen')
@section('og_description', 'CodePen shares your code publicly for show-and-tell. html.cloud delivers a finished HTML file privately, encrypted in your browser. Here is how they differ.')
@section('canonical', config('app.url') . '/vs/codepen')

@push('head')
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "FAQPage",
  "mainEntity": [
    {
      "@@type": "Question",
      "name": "What is the difference between html.cloud and CodePen?",
      "acceptedAnswer": { "@@type": "Answer", "text": "CodePen is a social front-end editor for writing, showing, and iterating HTML, CSS, and JavaScript — the code itself is the point, and pens are public by default. html.cloud delivers a finished, self-contained HTML file privately: the recipient sees the rendered page, the file is encrypted in your browser, and no account is needed." }
    },
    {
      "@@type": "Question",
      "name": "Are CodePen pens private?",
      "acceptedAnswer": { "@@type": "Answer", "text": "On CodePen, pens are public by default; private pens require a paid PRO plan. html.cloud links are private by design — the file is encrypted client-side and the key lives in the URL fragment, which is never sent to the server." }
    },
    {
      "@@type": "Question",
      "name": "Can I share an AI-generated HTML file with CodePen?",
      "acceptedAnswer": { "@@type": "Answer", "text": "You can paste code into a pen, but CodePen is built around editing and showing source code publicly. To privately hand a finished AI-generated HTML file (a report, presentation, or dashboard) to a specific person, html.cloud is the better fit: drop the file, get a private encrypted link." }
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
  <strong>CodePen</strong> is a public front-end playground for writing and showing <em>code</em>.
  <strong>html.cloud</strong> privately delivers a <em>finished</em> HTML file — the recipient sees
  the rendered page, not the source — encrypted in your browser, with no account.
</p>

<section class="content-section">
  <h2 class="content-h2">At a glance</h2>
  <table class="content-table compare-table">
    <thead>
      <tr><th></th><th class="col-us">html.cloud</th><th>CodePen</th></tr>
    </thead>
    <tbody>
      <tr><td>What it's for</td><td class="col-us">Privately delivering a finished HTML file</td><td>Writing, showing &amp; iterating front-end code</td></tr>
      <tr><td>What the recipient sees</td><td class="col-us">The rendered page</td><td>An editor with your source code + preview</td></tr>
      <tr><td>Default visibility</td><td class="col-us">Private — only people with the link</td><td>Public; private pens need a paid plan</td></tr>
      <tr><td>Privacy model</td><td class="col-us">Client-side AES-256-GCM; we store only ciphertext</td><td>Code stored and shown in plaintext</td></tr>
      <tr><td>Account</td><td class="col-us">None</td><td>Account needed to save and manage pens</td></tr>
      <tr><td>Live editing / community</td><td class="col-us">No — replace the file via a private edit link</td><td>Yes — in-browser editing, embeds, a community</td></tr>
    </tbody>
  </table>
</section>

<section class="content-section">
  <h2 class="content-h2">When to use which</h2>
  <p class="content-p">
    <strong>Use CodePen</strong> when the <em>code</em> is the point: prototyping in the browser,
    sharing a technique, embedding a live demo in an article, teaching, or collaborating with other
    developers. It's a great place to show how something is built.
  </p>
  <p class="content-p">
    <strong>Use html.cloud</strong> when the <em>result</em> is the point and it needs to stay private:
    handing a client or colleague a finished HTML report, presentation, or dashboard. They open a link
    and see the page — no editor, no public listing, no sign-in — and the file is encrypted before it
    ever leaves your device.
  </p>
</section>

<section class="content-section">
  <h2 class="content-h2">FAQ</h2>
  <div class="faq">
    <details class="faq-item">
      <summary>What's the difference between html.cloud and CodePen?</summary>
      <p>CodePen is a social editor for writing and showing code publicly. html.cloud delivers a finished
        HTML file privately — the recipient sees the rendered page, the file is encrypted in your browser,
        and no account is needed.</p>
    </details>
    <details class="faq-item">
      <summary>Are CodePen pens private?</summary>
      <p>Pens are public by default; private pens require a paid PRO plan. html.cloud links are private by
        design — encrypted client-side with the key in the URL fragment, never sent to the server.</p>
    </details>
    <details class="faq-item">
      <summary>Can I share an AI-generated HTML file with CodePen?</summary>
      <p>You can paste code into a pen, but CodePen centres on editing and showing source publicly. To hand
        a finished AI-generated file to a specific person privately, html.cloud fits better: drop the file,
        get a private encrypted link.</p>
    </details>
  </div>
</section>

@include('partials.compare-links', ['current' => 'vs.codepen'])

<div class="content-cta">
  <a href="{{ route('home') }}" class="content-cta-btn">Go to the start page to share an HTML file →</a>
</div>
@endsection
