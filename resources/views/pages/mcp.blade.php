@extends('pages.content')

@section('title', 'Let Claude share your HTML for you — html.cloud for Claude Desktop')
@section('description', 'Add html.cloud to Claude so it can privately share the HTML it makes for you — automatically, with a link. One-click install for Claude Desktop, no account, no setup files. Encrypted before upload.')
@section('og_title', 'Let Claude share your HTML privately')
@section('og_description', 'A one-click Claude Desktop extension that lets Claude turn the HTML it generates into a private, encrypted share link. No account, no setup files.')
@section('canonical', config('app.url') . '/mcp')

@php
  // Versioned GitHub release asset — stable across future releases.
  $mcpbUrl = 'https://github.com/viljamilaurila/html-cloud/releases/download/mcp-v0.1.0/html-cloud.mcpb';
@endphp

@push('head')
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "SoftwareApplication",
  "name": "html.cloud for Claude Desktop",
  "url": "{{ config('app.url') }}/mcp",
  "applicationCategory": "DeveloperApplication",
  "operatingSystem": "macOS, Windows",
  "offers": { "@@type": "Offer", "price": "0", "priceCurrency": "USD" },
  "downloadUrl": "{{ $mcpbUrl }}",
  "description": "A Claude Desktop extension that lets Claude share generated HTML as a private, end-to-end encrypted link."
}
</script>
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "FAQPage",
  "mainEntity": [
    {
      "@@type": "Question",
      "name": "What does this let Claude do?",
      "acceptedAnswer": { "@@type": "Answer", "text": "After a one-time install, Claude can take HTML it made for you — a page, report, presentation, or invitation — and turn it into a private share link in the same conversation. The file is encrypted before upload, so html.cloud stores only ciphertext it cannot read, and no account is needed." }
    },
    {
      "@@type": "Question",
      "name": "Do I need to install anything technical?",
      "acceptedAnswer": { "@@type": "Answer", "text": "No. For Claude Desktop you download one file and double-click it — Claude Desktop installs it and includes everything it needs to run, so there is no separate software to set up and no config files to edit." }
    },
    {
      "@@type": "Question",
      "name": "Is my HTML private when Claude shares it?",
      "acceptedAnswer": { "@@type": "Answer", "text": "Yes. It is encrypted with AES-256-GCM on your computer before anything is uploaded. The key to read it lives in the share link after the # and is never sent to a server, so html.cloud only ever stores ciphertext." }
    },
    {
      "@@type": "Question",
      "name": "Does it cost anything or need an account?",
      "acceptedAnswer": { "@@type": "Answer", "text": "No. The extension and html.cloud are free, and there is no account to create." }
    }
  ]
}
</script>
@endpush

@section('page')
<div class="content-eyebrow">For Claude</div>
<h1 class="content-title">Let Claude share your HTML for you</h1>

<p class="content-lead">
  When you've made something with Claude — a report, a proposal, a page — you can
  just ask Claude to <em>share it privately</em>, and it hands you a link to send on.
  <strong>No saving files, no account, no setup files.</strong>
</p>

<div class="mcp-demo" aria-label="Example conversation with Claude">
  <div class="mcp-turn">
    <span class="mcp-who">You</span>
    <div class="mcp-msg mcp-msg-you">
      This proposal looks great. Can you share it privately so I can send the client a link?
    </div>
  </div>
  <div class="mcp-turn">
    <span class="mcp-who">Claude</span>
    <div class="mcp-msg mcp-msg-claude">
      Here's a private link to the proposal — anyone you send it to can open it, and it expires in 30 days:
      <span class="mcp-demo-link">html.cloud/v/kT4eN7xQ#b3FvXy…</span>
    </div>
  </div>
</div>

<p class="content-p mcp-demo-note">
  Claude encrypts the page on your computer before it's uploaded, so not even we can
  read it. <a href="{{ route('security') }}">See how that works →</a>
</p>

<section class="content-section">
  <h2 class="content-h2">Add it to Claude Desktop</h2>
  <p class="content-p">One file, one click — Claude Desktop has everything it needs built in.</p>
  <ol class="steps">
    <li class="step">
      <span class="step-num">1</span>
      <div class="step-body">
        <p><strong>Download the html.cloud extension.</strong></p>
        <a class="btn-download" href="{{ $mcpbUrl }}">
          <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M8 2v8M4.5 6.5 8 10l3.5-3.5M3 13h10"/></svg>
          Download for Claude Desktop
        </a>
        <p class="btn-sub">A <code>.mcpb</code> file · macOS &amp; Windows</p>
      </div>
    </li>
    <li class="step">
      <span class="step-num">2</span>
      <div class="step-body">
        <p><strong>Double-click the downloaded file.</strong> Claude Desktop opens and offers to install it. (If it doesn't, open Claude's <em>Settings → Extensions</em> and drag the file in.)</p>
      </div>
    </li>
    <li class="step">
      <span class="step-num">3</span>
      <div class="step-body">
        <p><strong>Click Install.</strong> That's it — no accounts, no terminal, no settings to edit. The first time Claude uses it, you'll be asked to allow it; click <em>Allow</em>.</p>
      </div>
    </li>
  </ol>
</section>

<section class="content-section">
  <h2 class="content-h2">Try it</h2>
  <p class="content-p">Make something with Claude, then ask:</p>
  <pre class="content-codeblock"><code>Share this privately with html.cloud and give me the link.</code></pre>
  <p class="content-p">
    By default the link expires in <strong>30 days</strong>. Want it shorter or permanent?
    Just say so — “with a 7-day expiry,” or “that never expires.” Claude also gives you a
    private <em>edit link</em> with each share, so you can replace, re-expire, or delete the
    page anytime.
  </p>
</section>

<section class="content-section">
  <h2 class="content-h2">Using Claude Code or another app?</h2>
  <p class="content-p">
    If you use Claude Code, add it with one command (this path uses
    <a href="https://nodejs.org" rel="noopener" target="_blank">Node.js</a> 20+):
  </p>
  <pre class="content-codeblock"><code>claude mcp add html-cloud -- npx -y html-cloud-mcp</code></pre>
  <details class="faq-item">
    <summary>Manual setup for other MCP apps</summary>
    <p>Any app that supports the Model Context Protocol can run the server via <code>npx</code>.
      Add this to its MCP config (requires Node.js 20+):</p>
    <pre class="content-codeblock"><code>{
  "mcpServers": {
    "html-cloud": {
      "command": "npx",
      "args": ["-y", "html-cloud-mcp"]
    }
  }
}</code></pre>
  </details>
</section>

<section class="content-section">
  <h2 class="content-h2">Questions</h2>
  <div class="faq">
    <details class="faq-item">
      <summary>Is my HTML really private?</summary>
      <p>Yes. It's encrypted with AES-256-GCM on your own computer before anything is uploaded. The key to read it lives in the link after the <code>#</code> and is never sent to us, so html.cloud only ever stores scrambled bytes it can't read. The extension is <a href="https://github.com/viljamilaurila/html-cloud" rel="noopener" target="_blank">open source</a>, so anyone can verify that.</p>
    </details>
    <details class="faq-item">
      <summary>Do I need an account, or to pay?</summary>
      <p>No. There's no sign-up for html.cloud and nothing to pay. You just share links.</p>
    </details>
    <details class="faq-item">
      <summary>Do I need to install Node or anything else?</summary>
      <p>Not for Claude Desktop — it includes everything the extension needs. (The Claude Code and manual setups above use Node.js, which is a free one-time install.)</p>
    </details>
    <details class="faq-item">
      <summary>Who can see a page I shared?</summary>
      <p>Only people you send the link to — the link is the key. Treat it like a password: share it through a trusted channel, and set an expiry for anything sensitive.</p>
    </details>
    <details class="faq-item">
      <summary>Can I change or remove a page after sharing?</summary>
      <p>Yes. Every share comes with a private edit link. Open it to replace the page (the share link stays the same), change when it expires, or delete it right away.</p>
    </details>
    <details class="faq-item">
      <summary>Prefer to do it yourself?</summary>
      <p>You can drop a file on the <a href="{{ route('home') }}">homepage</a> or use the <a href="{{ route('cli') }}">command line</a> — same encryption, same private links.</p>
    </details>
  </div>
</section>
@endsection
