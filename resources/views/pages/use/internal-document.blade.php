@extends('pages.content')

@section('title', 'Share an internal HTML document securely — zero-knowledge, expiring link')
@section('description', 'Share an internal HTML document, dashboard, or runbook with your team through a zero-knowledge encrypted link. It is encrypted in your browser, can expire automatically, and is never readable by us. How it works, plus an FAQ.')
@section('og_title', 'Share an internal HTML document securely')
@section('og_description', 'Share an internal HTML doc or dashboard as a zero-knowledge encrypted link — encrypted in your browser, expiring, never readable by us.')
@section('canonical', config('app.url') . '/share-internal-document')

@push('head')
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "FAQPage",
  "mainEntity": [
    {
      "@@type": "Question",
      "name": "Is html.cloud safe for internal documents?",
      "acceptedAnswer": { "@@type": "Answer", "text": "It is zero-knowledge: the document is encrypted in your browser with AES-256-GCM before upload, and the key never reaches the server, so we only ever store ciphertext. Because anyone with the link can open it, share the link over a channel your team already trusts and set an expiry for sensitive material." }
    },
    {
      "@@type": "Question",
      "name": "Can I make an internal document expire automatically?",
      "acceptedAnswer": { "@@type": "Answer", "text": "Yes. You can set a document to expire after 7 or 30 days, keep it indefinitely, or delete it at any time." }
    },
    {
      "@@type": "Question",
      "name": "Who can access the document?",
      "acceptedAnswer": { "@@type": "Answer", "text": "Anyone who has the full link, including the part after the #. The link is the credential, so distribute it through trusted internal channels and use an expiry rather than relying on the link staying secret forever." }
    }
  ]
}
</script>
@endpush

@section('page')
<div class="content-eyebrow">Use case</div>
<div class="content-image-container">
  @include('partials.illustrations.internal-doc')
</div>
<h1 class="content-title">Share an internal HTML document securely</h1>

<p class="content-lead">
  Share an internal document, dashboard, or runbook with your team through a
  <strong>zero-knowledge</strong> encrypted link. It's encrypted in your browser, can expire
  automatically, and is never readable by us.
</p>

<div class="content-cta content-cta-top">
  <a href="{{ route('home') }}" class="content-cta-btn">Share an internal document now →</a>
</div>

<section class="content-section">
  <h2 class="content-h2">Keep internal work off public URLs</h2>
  <p class="content-p">
    Internal HTML — a status dashboard, an incident runbook, a draft spec — shouldn't sit on a public
    address where it could be found, and not every team wants it stored in a third party's plaintext.
    html.cloud encrypts the file on your device, so the server only ever holds ciphertext, and gives you
    a link that opens the page. Set it to expire so stale material doesn't linger.
  </p>
</section>

@include('partials.how-it-works')

@include('partials.privacy-note')

<section class="content-section">
  <h2 class="content-h2">FAQ</h2>
  <div class="faq">
    <details class="faq-item">
      <summary>Is html.cloud safe for internal documents?</summary>
      <p>It's zero-knowledge — encrypted in your browser with AES-256-GCM before upload, with the key never
        reaching the server. Since anyone with the link can open it, share the link over a trusted channel
        and set an expiry for sensitive material.</p>
    </details>
    <details class="faq-item">
      <summary>Can I make an internal document expire automatically?</summary>
      <p>Yes — set it to expire after 7 or 30 days, keep it indefinitely, or delete it anytime.</p>
    </details>
    <details class="faq-item">
      <summary>Who can access the document?</summary>
      <p>Anyone with the full link, including the part after the <code>#</code>. The link is the credential,
        so distribute it through trusted internal channels and lean on expiry rather than secrecy alone.</p>
    </details>
  </div>
</section>

@include('partials.use-links', ['current' => 'use.internal'])

<div class="content-cta">
  <a href="{{ route('home') }}" class="content-cta-btn">Go to the start page to share an HTML file →</a>
</div>
@endsection
