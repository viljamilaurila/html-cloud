@extends('pages.content')

@section('title', 'Send a client a private report or proposal — encrypted HTML link, no login')
@section('description', 'Send a client a polished HTML report or proposal through a private link — no public URL and no login for them. It is encrypted in your browser, can expire on a deadline, and can be updated without resending. How it works, plus an FAQ.')
@section('og_title', 'Send a client a private report or proposal')
@section('og_description', 'Deliver a client report or proposal as a private, encrypted link — no public URL, no login for the client, with expiry and updates.')
@section('canonical', config('app.url') . '/send-private-client-report')

@push('head')
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "FAQPage",
  "mainEntity": [
    {
      "@@type": "Question",
      "name": "How do I send a client a report without a public URL?",
      "acceptedAnswer": { "@@type": "Answer", "text": "Drop the report as an HTML file on html.cloud. You get a private link with the decryption key in its URL fragment — there is no public, guessable address. Send the link to your client and they open it in their browser, no login required." }
    },
    {
      "@@type": "Question",
      "name": "Does my client need an account to view it?",
      "acceptedAnswer": { "@@type": "Answer", "text": "No. Anyone with the link can open the report — there is no sign-up for you or for them." }
    },
    {
      "@@type": "Question",
      "name": "Can I update or expire the report after sending it?",
      "acceptedAnswer": { "@@type": "Answer", "text": "Yes. You can replace the file behind the same link so the client always sees the latest version, set it to expire after 7 or 30 days for time-sensitive proposals, or delete it entirely." }
    }
  ]
}
</script>
@endpush

@section('page')
<div class="content-eyebrow">Use case</div>
<div class="content-image-container">
  @include('partials.illustrations.presenter')
</div>
<h1 class="content-title">Send a client a private report or proposal</h1>

<p class="content-lead">
  Deliver a polished HTML report or proposal through a <em>private</em> link — no public URL, no login
  for your client. It's encrypted in your browser, can <strong>expire on a deadline</strong>, and can be
  updated without sending a new file.
</p>

<div class="content-cta content-cta-top">
  <a href="{{ route('home') }}" class="content-cta-btn">Send a private report now →</a>
</div>

<section class="content-section">
  <h2 class="content-h2">Built for confidential client work</h2>
  <p class="content-p">
    Client reports and proposals are confidential, and they often shouldn't live on a public URL or sit
    in an inbox forever. html.cloud lets you hand over a single link that opens the document in the
    browser, encrypted so only your client can read it. For a time-sensitive proposal, set it to expire;
    if the numbers change, replace the file behind the same link so they always see the current version.
  </p>
</section>

@include('partials.how-it-works')

@include('partials.privacy-note')

<section class="content-section">
  <h2 class="content-h2">FAQ</h2>
  <div class="faq">
    <details class="faq-item">
      <summary>How do I send a client a report without a public URL?</summary>
      <p>Drop the report as an HTML file on html.cloud. You get a private link with the key in its
        <code>#</code> fragment — no public, guessable address. Your client opens it in the browser, no
        login required.</p>
    </details>
    <details class="faq-item">
      <summary>Does my client need an account to view it?</summary>
      <p>No — anyone with the link can open the report. There's no sign-up for you or for them.</p>
    </details>
    <details class="faq-item">
      <summary>Can I update or expire the report after sending it?</summary>
      <p>Yes. Replace the file behind the same link so the client always sees the latest version, set it to
        expire after 7 or 30 days, or delete it entirely.</p>
    </details>
  </div>
</section>

@include('partials.use-links', ['current' => 'use.report'])

<div class="content-cta">
  <a href="{{ route('home') }}" class="content-cta-btn">Go to the start page to share an HTML file →</a>
</div>
@endsection
