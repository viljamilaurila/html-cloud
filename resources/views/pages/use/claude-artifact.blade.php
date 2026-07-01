@extends('pages.content')

@section('title', 'Share a Claude artifact privately — encrypted link, no account')
@section('description', 'Claude built you an HTML artifact — a report, dashboard, or mini-app. Drop it on html.cloud and get a private link to send anyone, encrypted in your browser before upload, with no account. Here is how, plus an FAQ.')
@section('og_title', 'Share a Claude artifact privately')
@section('og_description', 'Drop a Claude HTML artifact and get a private, encrypted link — no account, no public URL. Only people you send the link to can open it.')
@section('canonical', config('app.url') . '/share-claude-artifact')

@push('head')
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "FAQPage",
  "mainEntity": [
    {
      "@@type": "Question",
      "name": "How do I share a Claude artifact privately?",
      "acceptedAnswer": { "@@type": "Answer", "text": "Export or download the artifact as an HTML file, then drop it on html.cloud. Your browser encrypts it and gives you a private link. Send that link to anyone — they open the page in their browser, with no account and no public URL." }
    },
    {
      "@@type": "Question",
      "name": "Can the person I share with see my Claude account or conversation?",
      "acceptedAnswer": { "@@type": "Answer", "text": "No. html.cloud only ever has the single HTML file you upload, encrypted. It has no connection to your Claude account, your chats, or anything else — the recipient sees just the page." }
    },
    {
      "@@type": "Question",
      "name": "Do I or the recipient need an account?",
      "acceptedAnswer": { "@@type": "Answer", "text": "Neither. There is no sign-up. You drop the file and share the link; anyone with the link can open it." }
    }
  ]
}
</script>
@endpush

@section('page')
<div class="content-eyebrow">Use case</div>
<div class="content-image-container">
  @include('partials.illustrations.artifact')
</div>
<h1 class="content-title">Share a Claude artifact privately</h1>

<p class="content-lead">
  Claude built you an HTML artifact — a report, a dashboard, a little app. Drop it on
  <strong>html.cloud</strong> and get a <em>private</em> link you can send to anyone. It's encrypted
  in your browser before upload, with no account and no public URL.
</p>

<div class="content-callout content-callout-top">
  <strong>Using Claude Desktop?</strong> Skip the download entirely — connect html.cloud to
  Claude once, then just ask it to share what it made. <a href="{{ route('mcp') }}">Let Claude share for you →</a>
</div>

<section class="content-section">
  <h2 class="content-h2">Why not just send the file?</h2>
  <p class="content-p">
    A Claude artifact is a self-contained HTML file, which is awkward to pass around. Emailing it as an
    attachment often gets the file blocked or flagged; dropping it on a public host like
    <a href="{{ route('vs.netlify') }}">Netlify Drop</a> puts it at a public URL anyone could stumble
    onto. html.cloud is built for exactly this: a private link that opens the page, encrypted so that
    only the people you send it to can read it.
  </p>
</section>

@include('partials.how-it-works')

@include('partials.privacy-note')

<section class="content-section">
  <h2 class="content-h2">FAQ</h2>
  <div class="faq">
    <details class="faq-item">
      <summary>How do I share a Claude artifact privately?</summary>
      <p>Download the artifact as an HTML file, then drop it on html.cloud. Your browser encrypts it and
        gives you a private link — send it to anyone, and they open the page with no account and no public
        URL.</p>
    </details>
    <details class="faq-item">
      <summary>Can the person I share with see my Claude account or conversation?</summary>
      <p>No. html.cloud only has the single encrypted file you upload — no link to your Claude account or
        chats. The recipient sees just the page.</p>
    </details>
    <details class="faq-item">
      <summary>Do I or the recipient need an account?</summary>
      <p>Neither — there's no sign-up. You drop the file and share the link; anyone with the link can open
        it.</p>
    </details>
  </div>
</section>

@include('partials.use-links', ['current' => 'use.claude'])

<div class="content-cta">
  <a href="{{ route('home') }}" class="content-cta-btn">Go to the start page to share an HTML file →</a>
</div>
@endsection
