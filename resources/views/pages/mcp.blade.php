@extends('pages.content')

@section('title', 'Let Claude share your HTML for you — html.cloud for Claude Desktop')
@section('description', 'Add html.cloud to Claude so it can privately share the HTML it makes for you — automatically, with a link. One-click install for Claude Desktop, no account, no setup files. Encrypted before upload.')
@section('og_title', 'Let Claude share your HTML privately')
@section('og_description', 'A one-click Claude Desktop extension that lets Claude turn the HTML it generates into a private, encrypted share link. No account, no setup files.')
@section('canonical', config('app.url') . '/mcp')

@php
  // Versioned GitHub release asset — stable across future releases.
  $mcpbUrl = 'https://github.com/viljamilaurila/html-cloud/releases/download/mcp-v0.4.0/html-cloud.mcpb';
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
      "acceptedAnswer": { "@@type": "Answer", "text": "After a one-time install, Claude can take HTML it made for you — a page, report, presentation, or invitation — and turn it into a private share link in the same conversation. Ask for a change afterwards and Claude updates the page at the same link. The file is encrypted before upload, so html.cloud stores only ciphertext it cannot read, and no account is needed." }
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

<div class="mcp-demo" id="mcp-demo" aria-label="Example conversation with Claude">
  <div class="mcp-turn mcp-anim" style="--t: .4s">
    <span class="mcp-who">You</span>
    <div class="mcp-msg mcp-msg-you">
      This proposal looks great. Can you share it privately so I can send the client a link?
    </div>
  </div>
  <div class="mcp-turn mcp-anim" style="--t: 1.5s; --t2: 3.1s">
    <span class="mcp-who">Claude</span>
    <div class="mcp-msg mcp-msg-claude">
      <span class="mcp-dots" aria-hidden="true"><i></i><i></i><i></i></span>
      <div class="mcp-text"><div>
        Here's a private link to the proposal — anyone you send it to can open it, and it expires in 30 days:
        <span class="mcp-demo-link">html.cloud/v/kT4eN7xQ#b3FvXy…</span>
      </div></div>
    </div>
  </div>
  <div class="mcp-turn mcp-anim" style="--t: 5.4s">
    <span class="mcp-who">You</span>
    <div class="mcp-msg mcp-msg-you">
      Perfect. Could you make the title bigger and add a short “next steps” section at the end?
    </div>
  </div>
  <div class="mcp-turn mcp-anim" style="--t: 6.5s; --t2: 8.3s">
    <span class="mcp-who">Claude</span>
    <div class="mcp-msg mcp-msg-claude">
      <span class="mcp-dots" aria-hidden="true"><i></i><i></i><i></i></span>
      <div class="mcp-text"><div>
        Done — I updated the page in place. The link is the same one, so the client will see the new version:
        <span class="mcp-demo-link">html.cloud/v/kT4eN7xQ#b3FvXy… <span class="mcp-demo-tag">same link</span></span>
      </div></div>
    </div>
  </div>
  <button type="button" class="mcp-replay mcp-anim" style="--t: 9.4s" hidden>
    <svg width="12" height="12" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2.5 8a5.5 5.5 0 1 0 1.6-3.9"/><path d="M2.5 2.5v3h3"/></svg>
    Replay
  </button>
</div>
<script nonce="{{ Vite::cspNonce() }}">
(() => {
  // Progressive: without JS the conversation plays once on load. With JS it
  // waits until the card is on screen, and gets a Replay button.
  const demo = document.getElementById('mcp-demo');
  const replay = demo.querySelector('.mcp-replay');
  if (matchMedia('(prefers-reduced-motion: reduce)').matches) return;

  demo.classList.add('is-waiting');
  new IntersectionObserver((entries, observer) => {
    if (!entries.some(e => e.isIntersecting)) return;
    demo.classList.remove('is-waiting');
    observer.disconnect();
  }, { threshold: 0.2 }).observe(demo);

  replay.hidden = false;
  replay.addEventListener('click', () => {
    const running = document.getAnimations?.().filter(a => demo.contains(a.effect.target)) ?? [];
    if (running.length) {
      running.forEach(a => { a.cancel(); a.play(); }); // back to time zero, delays included
      return;
    }
    demo.classList.add('is-reset');
    void demo.offsetWidth; // flush so the animations restart from zero
    demo.classList.remove('is-reset');
  });
})();
</script>

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
        <p><strong>Double-click the downloaded file.</strong> Claude Desktop opens and offers to install it. (If it doesn't, open Claude's <em>Settings → Extensions</em>, click <em>Advanced settings</em>, and use <em>Install Extension…</em> under <em>Extension Developer</em> to pick the file — that works even when drag-and-drop doesn't.)</p>
      </div>
    </li>
    <li class="step">
      <span class="step-num">3</span>
      <div class="step-body">
        <p><strong>Click Install.</strong> That's it — no accounts, no terminal, no settings to edit. The first time Claude uses it, you'll be asked to allow it; click <em>Allow</em>.</p>
        <p><strong>About the red warning.</strong> Claude Desktop shows the same "access to everything on your computer" box for every extension installed from a file rather than from its built-in directory — it isn't about html.cloud specifically. This one is open source, about 250 lines you can <a href="https://github.com/viljamilaurila/html-cloud/tree/main/mcp" rel="noopener" target="_blank">read on GitHub</a>, talks to one host (html.cloud), and sends only encrypted content. Extensions installed from Claude's directory don't show the warning; we're working on getting listed.</p>
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
  <p class="content-p">Changed your mind about something? Keep talking:</p>
  <pre class="content-codeblock"><code>Make the title bigger and add a section on next steps, then update the page.</code></pre>
  <p class="content-p">
    Claude revises the HTML and updates the page in place. The share link you already
    sent stays exactly the same — whoever opens it now sees the new version.
  </p>
</section>

<section class="content-section">
  <h2 class="content-h2">Artifacts or html.cloud?</h2>
  <p class="content-p">
    Both, and Claude sorts it out. Artifacts stay the default for building pages and for
    anything meant to be public. html.cloud steps in when the page is sensitive or has to
    reach someone outside your organisation.
  </p>
  @include('partials.route-demo')

  <p class="content-p">
    That switch is real — you'll see it when you install the extension (manual setups use
    <code>HTML_CLOUD_PREFER=always</code>). <a href="{{ route('vs.artifacts') }}">More on when to use which →</a>
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
      <p>Yes. Just ask Claude to change the page — it updates it at the same share link, so nobody needs a new one. Every share also comes with a private edit link you can open yourself to replace the page, change when it expires, or delete it right away.</p>
    </details>
    <details class="faq-item">
      <summary>Why does Claude Desktop warn that it can access everything on my computer?</summary>
      <p>Because it's installed from a file, not from Claude's built-in extension directory — Claude Desktop shows that same box for every extension installed that way, whoever made it. It's an honest statement about how extensions run, not a finding about this one. What this extension actually does is public: about 250 lines of open-source code on <a href="https://github.com/viljamilaurila/html-cloud/tree/main/mcp" rel="noopener" target="_blank">GitHub</a>, one outbound host (html.cloud), and the only thing it uploads is ciphertext (<a href="{{ route('mcp.privacy') }}">privacy policy</a>). If you'd rather not install anything, the <a href="{{ route('home') }}">homepage</a> does the same job in your browser.</p>
    </details>
    <details class="faq-item">
      <summary>Does it work in Cowork?</summary>
      <p>Yes, in Cowork sessions linked to the computer the extension is installed on — Claude Desktop passes the extension through to the session. It won't appear in cloud-only Cowork sessions that aren't linked to a computer; for those, ask for your share link in a regular chat, or paste the finished HTML into one and ask Claude to share it.</p>
    </details>
    <details class="faq-item">
      <summary>Prefer to do it yourself?</summary>
      <p>You can drop a file on the <a href="{{ route('home') }}">homepage</a> or use the <a href="{{ route('cli') }}">command line</a> — same encryption, same private links.</p>
    </details>
  </div>
</section>
@endsection
