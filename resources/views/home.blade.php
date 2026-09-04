@extends('layout')
@section('title', 'html.cloud — Private HTML file sharing')
@section('description', 'Share an HTML file with a private link. Encrypted in your browser before upload — only people with your link can read it. Not even us. No sign-up.')
@section('og_title', 'html.cloud — Private HTML file sharing')
@section('og_description', 'Drop an HTML file, get a private link. Encrypted in your browser. Built for AI-generated presentations and sensitive documents.')
@section('canonical', config('app.url'))

@push('head')
<link href="https://fonts.googleapis.com/css2?family=Caveat:wght@600;700&display=swap" rel="stylesheet">
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "WebSite",
  "name": "html.cloud",
  "alternateName": ["html cloud", "HTML Cloud", "htmlcloud"],
  "url": "{{ config('app.url') }}"
}
</script>
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "Organization",
  "name": "html.cloud",
  "url": "{{ config('app.url') }}",
  "logo": "{{ config('app.url') }}/apple-touch-icon.png",
  "sameAs": [
    "https://github.com/viljamilaurila/html-cloud",
    "https://www.npmjs.com/package/html-cloud"
  ]
}
</script>
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "WebApplication",
  "name": "html.cloud",
  "alternateName": "html cloud",
  "url": "{{ config('app.url') }}",
  "description": "Private HTML file sharing. Drop an HTML file and get a shareable link — encrypted in your browser with AES-256-GCM before upload. Only people with the link can read the file.",
  "applicationCategory": "Utilities",
  "operatingSystem": "Any",
  "offers": { "@@type": "Offer", "price": "0", "priceCurrency": "USD" }
}
</script>
@endpush

@section('content')
<div class="shell home-swiss">
  @include('partials.topbar')

  <main class="home-main">
    <div class="hero">
      <h1 class="headline">Share an HTML file in seconds. <em>Private by design.</em></h1>
      <div class="hero-art" aria-hidden="true">@include('partials.illustrations.hero-window')</div>
    </div>

    <div class="dropzone" id="dropzone">
      {{-- handwritten aside in the left margin; its arrow crosses the border into the zone.
           Hidden on viewports without a margin (see CSS). The bold label is the real instruction. --}}
      <div class="dropzone-note" aria-hidden="true">
        <span class="dropzone-note-text">drop it here</span>
        <svg class="dropzone-note-arrow" viewBox="0 0 210 100" fill="none" stroke="currentColor"
             stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round">
          <path d="M8 8 C24 60 92 96 196 66"/>
          <path d="M180 48 L198 66 L178 82"/>
        </svg>
      </div>
      <span class="dropzone-tag" aria-hidden="true">one .html file · up to 10 MB</span>
      @foreach (['top:22%;left:17%', 'top:28%;right:15%;transition-delay:.06s', 'bottom:24%;left:27%;transition-delay:.12s', 'bottom:30%;right:23%;transition-delay:.09s'] as $pos)
        <span class="dropzone-spark" style="{{ $pos }}" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 1c1 5 3 7 8 8-5 1-7 3-8 8-1-5-3-7-8-8 5-1 7-3 8-8z"/></svg>
        </span>
      @endforeach
      <div class="dropzone-art" aria-hidden="true">@include('partials.illustrations.drop-file')</div>
      <div class="dropzone-label">Drop an HTML file here</div>
      <div class="dropzone-sub">
        or <label for="file-input" class="file-link">choose from your computer</label>
        <input type="file" id="file-input" accept=".html,.htm" style="display:none">
      </div>

      <div class="dropzone-hover-overlay" aria-hidden="true"><span>Drop to encrypt &amp; share</span></div>
    </div>

    <div class="uploading-state hidden" id="uploading-state">
      <div class="uploading-spinner"></div>
      <span class="uploading-text">Encrypting &amp; uploading…</span>
    </div>

    <p class="explainer-cli">
      or from your terminal: <a href="{{ route('cli') }}"><code>npx html-cloud ./file.html</code></a>
    </p>
    <p class="explainer-cli explainer-cli-alt">
      <a href="{{ route('mcp') }}" class="explainer-cli-claude">or let Claude share for you →</a>
    </p>

    <section class="explainer">
      @include('partials.illustrations.flow')
      <p class="explainer-caption">
        Your browser locks the file before anything leaves your computer.
        The only key is in the link you share — we never see it, so not even we can read your file.
        <a href="{{ route('security') }}" class="explainer-readmore">How the encryption works →</a>
      </p>
    </section>

    <section class="home-info">
      <h2 class="home-info-h2">Private HTML file sharing, in plain terms</h2>
      <p class="home-info-p">
        html.cloud shares a single HTML file through a private link. The person you send it
        to just sees the page in their browser: no download, no sign-up, nothing to install
        on either side. And because the file is encrypted before it leaves your machine,
        <a href="{{ route('security') }}">not even we can read it</a>.
      </p>

      <dl class="home-facts">
        <div class="home-fact">
          <dt>AES-256-GCM</dt>
          <dd>encrypted in your browser, before anything is uploaded</dd>
        </div>
        <div class="home-fact">
          <dt>Key after&nbsp;<span class="home-fact-accent">#</span></dt>
          <dd>the decryption key rides in the link — browsers never send it to a server</dd>
        </div>
        <div class="home-fact">
          <dt>0 accounts</dt>
          <dd>no sign-up for you, none for the person you share with</dd>
        </div>
        <div class="home-fact">
          <dt>7 / 30 days</dt>
          <dd>optional expiry — or replace and delete the file without changing its link</dd>
        </div>
      </dl>

      <h3 class="home-info-h3">Built for the HTML files AI makes for you</h3>
      <p class="home-info-p">
        Claude, ChatGPT, and Gemini increasingly hand you a finished HTML file — a report,
        a slide deck, a small app. html.cloud is the private way to pass it on.
      </p>
      <div class="home-uses">
        <a class="home-use-card" href="{{ route('use.claude') }}">
          <span class="home-use-art" aria-hidden="true">@include('partials.illustrations.artifact')</span>
          <span class="home-use-label">Share a Claude artifact</span>
          <span class="home-use-sub">Reports, dashboards, mini-apps — without publishing them.</span>
        </a>
        <a class="home-use-card" href="{{ route('use.presentation') }}">
          <span class="home-use-art" aria-hidden="true">@include('partials.illustrations.deck')</span>
          <span class="home-use-label">Share an AI presentation</span>
          <span class="home-use-sub">HTML slides that open in any browser, full screen.</span>
        </a>
        <a class="home-use-card" href="{{ route('use.report') }}">
          <span class="home-use-art" aria-hidden="true">@include('partials.illustrations.presenter')</span>
          <span class="home-use-label">Send a client a report</span>
          <span class="home-use-sub">A private link that can expire when the deal closes.</span>
        </a>
        <a class="home-use-card" href="{{ route('use.internal') }}">
          <span class="home-use-art" aria-hidden="true">@include('partials.illustrations.internal-doc')</span>
          <span class="home-use-label">Share an internal document</span>
          <span class="home-use-sub">Dashboards and runbooks that stay inside the team.</span>
        </a>
      </div>

      <h3 class="home-info-h3">When to use it over the usual routes</h3>
      <ul class="home-routes">
        <li class="home-route">
          <span class="home-route-mark" aria-hidden="true">✕</span>
          <span class="home-route-who"><a href="{{ route('vs.netlify') }}">Netlify Drop</a> &amp; <a href="{{ route('vs.codepen') }}">CodePen</a></span>
          <span class="home-route-what">put your file at a public URL anyone can stumble onto</span>
        </li>
        <li class="home-route">
          <span class="home-route-mark" aria-hidden="true">✕</span>
          <span class="home-route-who"><a href="{{ route('vs.drive') }}">Google Drive &amp; Dropbox</a></span>
          <span class="home-route-what">make an HTML file download instead of open as a page</span>
        </li>
        <li class="home-route">
          <span class="home-route-mark" aria-hidden="true">✕</span>
          <span class="home-route-who"><a href="{{ route('vs.email') }}">Email attachments</a></span>
          <span class="home-route-what">.html files are often blocked or flagged outright</span>
        </li>
        <li class="home-route home-route-us">
          <span class="home-route-mark" aria-hidden="true">→</span>
          <span class="home-route-who">html.cloud</span>
          <span class="home-route-what">a private, encrypted link that renders as the page</span>
        </li>
      </ul>
    </section>
  </main>

  @include('partials.footer', ['showAbout' => false])
</div>
@endsection

@push('scripts')
@vite('resources/js/upload.js')
@endpush
