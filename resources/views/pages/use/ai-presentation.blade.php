@extends('pages.content')

@section('title', 'Share an AI-generated presentation privately — HTML slides, encrypted link')
@section('description', 'Made an HTML presentation with Claude, ChatGPT, or Gemini? Drop it on html.cloud and share a private, encrypted link that opens the slides in any browser — no account, no public URL, no file to download. How it works, plus an FAQ.')
@section('og_title', 'Share an AI-generated presentation privately')
@section('og_description', 'Share an AI-built HTML presentation as a private, encrypted link that opens in the browser — interactivity intact, no account, no public URL.')
@section('canonical', config('app.url') . '/share-ai-presentation')

@push('head')
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "FAQPage",
  "mainEntity": [
    {
      "@@type": "Question",
      "name": "How do I share an AI-generated HTML presentation?",
      "acceptedAnswer": { "@@type": "Answer", "text": "Save the presentation as a single HTML file and drop it on html.cloud. You get a private link that opens the slides in any browser — no download, no account, no public URL." }
    },
    {
      "@@type": "Question",
      "name": "Do animations and interactivity still work?",
      "acceptedAnswer": { "@@type": "Answer", "text": "Yes. html.cloud serves the real HTML file, so anything self-contained in it — transitions, navigation, interactive charts — runs in the recipient's browser exactly as it would locally." }
    },
    {
      "@@type": "Question",
      "name": "Is the presentation private?",
      "acceptedAnswer": { "@@type": "Answer", "text": "Yes. It is encrypted in your browser before upload, and the decryption key lives in the link's URL fragment, which is never sent to the server. Only people with the link can open it." }
    }
  ]
}
</script>
@endpush

@section('page')
<div class="content-eyebrow">Use case</div>
<div class="content-image-container">
  @include('partials.illustrations.deck')
</div>
<h1 class="content-title">Share an AI-generated presentation</h1>

<p class="content-lead">
  Made an HTML slide deck with Claude, ChatGPT, or Gemini? Drop it on <strong>html.cloud</strong> and
  share a <em>private</em> link that opens the presentation in any browser — no file to download, no
  account, no public URL.
</p>

<div class="content-cta content-cta-top">
  <a href="{{ route('home') }}" class="content-cta-btn">Share a presentation now →</a>
</div>

<section class="content-section">
  <h2 class="content-h2">A link, not an attachment</h2>
  <p class="content-p">
    AI tools increasingly produce presentations as a single, self-contained HTML file. That's great for
    portability but awkward to send — attachments get blocked, and the recipient has to download and open
    a file they may not trust. With html.cloud you share a link instead: it opens the slides directly in
    the browser, animations and interactivity intact, and stays private to the people you send it to.
  </p>
</section>

@include('partials.how-it-works')

@include('partials.privacy-note')

<section class="content-section">
  <h2 class="content-h2">FAQ</h2>
  <div class="faq">
    <details class="faq-item">
      <summary>How do I share an AI-generated HTML presentation?</summary>
      <p>Save the deck as a single HTML file and drop it on html.cloud. You get a private link that opens
        the slides in any browser — no download, no account, no public URL.</p>
    </details>
    <details class="faq-item">
      <summary>Do animations and interactivity still work?</summary>
      <p>Yes — html.cloud serves the real HTML file, so anything self-contained in it (transitions,
        navigation, interactive charts) runs in the recipient's browser as it would locally.</p>
    </details>
    <details class="faq-item">
      <summary>Is the presentation private?</summary>
      <p>Yes. It's encrypted in your browser before upload, and the key lives in the link's <code>#</code>
        fragment, never sent to the server. Only people with the link can open it.</p>
    </details>
  </div>
</section>

@include('partials.use-links', ['current' => 'use.presentation'])

<div class="content-cta">
  <a href="{{ route('home') }}" class="content-cta-btn">Go to the start page to share an HTML file →</a>
</div>
@endsection
