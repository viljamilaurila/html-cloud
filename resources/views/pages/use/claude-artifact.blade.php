@extends('pages.content')

@section('title', 'How to share a Claude artifact privately (no account needed)')
@section('description', 'Claude built you an HTML artifact — a report, dashboard, or mini-app. Don\'t publish it to a public URL: drop it on html.cloud for a private, encrypted link anyone can open in a browser, no account on either side. Steps, how it compares to Claude\'s Publish button, and an FAQ.')
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
    },
    {
      "@@type": "Question",
      "name": "Why not just use Claude's Publish button?",
      "acceptedAnswer": { "@@type": "Answer", "text": "Publishing puts the artifact at a public URL — right for things meant to be public, but anyone the link reaches can view it and it is not encrypted. html.cloud encrypts the file in your browser before upload, so only people with your link can read it, and the link can expire or be deleted." }
    }
  ]
}
</script>
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "HowTo",
  "name": "How to share a Claude artifact privately",
  "step": [
    { "@@type": "HowToStep", "name": "Download the artifact from Claude", "text": "Open the artifact in Claude and download it as an HTML file — or copy the code and save it into a file ending in .html." },
    { "@@type": "HowToStep", "name": "Drop the file on html.cloud", "text": "Your browser encrypts the file with AES-256-GCM before anything is uploaded and gives you a private link." },
    { "@@type": "HowToStep", "name": "Send the link", "text": "The recipient opens the rendered page in any browser — no account, no download, no Claude access needed." }
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

<section class="content-section">
  <h2 class="content-h2">From Claude to a private link</h2>
  <ol class="steps">
    <li class="step">
      <span class="step-num">1</span>
      <p><strong>Get the artifact out of Claude.</strong> Open the artifact and download it as an HTML
        file — or copy the code and save it into a file ending in <code class="accent">.html</code>.
        Any Claude plan works; there's nothing to configure.</p>
    </li>
    <li class="step">
      <span class="step-num">2</span>
      <p><strong>Drop it on html.cloud.</strong> Your browser encrypts the file with AES-256-GCM before
        anything is uploaded, then hands you a private link. The decryption key sits after the
        <code class="accent">#</code> in that link and never reaches our servers.</p>
    </li>
    <li class="step">
      <span class="step-num">3</span>
      <p><strong>Send the link.</strong> The recipient sees the artifact as a working page in any
        browser — no account, no download, no Claude access needed. Set the link to expire, or replace
        the file later without changing the link.</p>
    </li>
  </ol>
</section>

<section class="content-section">
  <h2 class="content-h2">What about Claude's Publish button?</h2>
  <p class="content-p">
    Claude can publish an artifact to a public URL, and for something meant to be public that's the
    right tool. The difference is exposure: a published artifact is viewable by anyone the link
    reaches — forwarded, pasted into a chat, or found later — and the file sits on the host's servers
    in readable form. html.cloud is the opposite default: the file is encrypted <em>before</em> it
    leaves your machine, we store only ciphertext, and the link can expire or be deleted when the
    conversation is over. For a client deliverable, an internal dashboard, or anything with real data
    in it, private-by-default is the safer starting point.
  </p>
</section>

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
    <details class="faq-item">
      <summary>Why not just use Claude's Publish button?</summary>
      <p>Publishing puts the artifact at a public URL — right for things meant to be public, but anyone
        the link reaches can view it and it isn't encrypted. html.cloud encrypts the file in your browser
        before upload, so only people with your link can read it, and the link can expire or be
        deleted.</p>
    </details>
  </div>
</section>

@include('partials.use-links', ['current' => 'use.claude'])

<div class="content-cta">
  <a href="{{ route('home') }}" class="content-cta-btn">Go to the start page to share an HTML file →</a>
</div>
@endsection
