@extends('pages.content')

@section('title', 'Share an HTML file from the command line — npx html-cloud')
@section('description', 'Share an HTML file from the terminal with one command: npx html-cloud file.html. The file is encrypted locally with AES-256-GCM before upload and you get a private share link. No install, no account, no public URL.')
@section('og_title', 'npx html-cloud — private HTML sharing from the terminal')
@section('og_description', 'One command encrypts your HTML file locally and returns a private share link. No install, no account. The server stores only ciphertext.')
@section('canonical', config('app.url') . '/cli')

@push('head')
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "SoftwareApplication",
  "name": "html-cloud CLI",
  "url": "{{ config('app.url') }}/cli",
  "applicationCategory": "DeveloperApplication",
  "operatingSystem": "macOS, Linux, Windows",
  "offers": { "@@type": "Offer", "price": "0", "priceCurrency": "USD" },
  "installUrl": "https://www.npmjs.com/package/html-cloud",
  "description": "Command-line tool that encrypts an HTML file locally with AES-256-GCM and uploads only ciphertext to html.cloud, returning a private share link."
}
</script>
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "FAQPage",
  "mainEntity": [
    {
      "@@type": "Question",
      "name": "How do I share an HTML file from the command line?",
      "acceptedAnswer": { "@@type": "Answer", "text": "Run npx html-cloud yourfile.html. The file is encrypted locally with AES-256-GCM, only the ciphertext is uploaded, and the command prints a private share link (copied to your clipboard) plus an edit link for replacing or deleting the file later. No install or account is needed — npx fetches the tool on first use." }
    },
    {
      "@@type": "Question",
      "name": "Is the file encrypted before it is uploaded?",
      "acceptedAnswer": { "@@type": "Answer", "text": "Yes. Encryption happens inside the CLI process on your machine, before any network request. The decryption key is placed after the # in the share link — that part of a URL is never sent to servers — so html.cloud stores only ciphertext it cannot read." }
    },
    {
      "@@type": "Question",
      "name": "Do I need a Node.js project or an account to use it?",
      "acceptedAnswer": { "@@type": "Answer", "text": "No project, no account. The only requirement is Node.js 20 or newer on your machine; npx runs the tool without installing anything permanently." }
    },
    {
      "@@type": "Question",
      "name": "Can I pipe HTML into it from another tool?",
      "acceptedAnswer": { "@@type": "Answer", "text": "Yes. Pass - as the filename to read from stdin, e.g. my-generator | npx html-cloud -. This makes it easy to share AI-generated HTML straight from a script or build step." }
    },
    {
      "@@type": "Question",
      "name": "Is the html-cloud CLI open source?",
      "acceptedAnswer": { "@@type": "Answer", "text": "Yes. The CLI lives in the same public GitHub repository as the html.cloud web client and shares the exact same crypto module, so you can verify that keys never leave your machine." }
    }
  ]
}
</script>
@endpush

@section('page')
<div class="content-eyebrow">Command line</div>
<h1 class="content-title">Share an HTML file from your terminal</h1>

<p class="content-lead">
  One command. The file is <em>encrypted on your machine</em> before anything is uploaded,
  and you get a private link — copied to your clipboard, ready to paste to someone.
  No install, no account, no public URL.
</p>

<pre class="content-codeblock"><code>npx html-cloud ./report.html</code></pre>

<pre class="content-codeblock content-codeblock-out"><code>Share link (anyone with this can view) — copied to clipboard:
  https://html.cloud/v/kT4eN7xQ#b3FvXyJq…

Edit link (keep private — replace, change expiry, delete):
  https://html.cloud/e/kT4eN7xQ#9dKw2mPv…

Encrypted locally with AES-256-GCM · expires in 30 days · the server never saw the keys</code></pre>

<section class="content-section">
  <h2 class="content-h2">What actually happens</h2>
  <ul class="content-list">
    <li><strong>Keys are generated locally.</strong> The CLI creates a random AES-256-GCM view key and an edit key inside the process on your machine.</li>
    <li><strong>The file is encrypted before any network request.</strong> Only ciphertext is uploaded — the same zero-knowledge model as the <a href="{{ route('home') }}">html.cloud website</a>, using literally the same open-source crypto module.</li>
    <li><strong>The keys travel only inside your links.</strong> They sit after the <code>#</code> in the URLs the command prints. That part of a URL never reaches a server.</li>
  </ul>
  <p class="content-p">
    The full model — including what we can and cannot see, and the honest limitations —
    is on the <a href="{{ route('security') }}">how encryption works</a> page.
  </p>
</section>

<section class="content-section">
  <h2 class="content-h2">Pipe straight from a generator</h2>
  <p class="content-p">
    Pass <code>-</code> to read from stdin. If a script or AI tool produces HTML,
    you can go from output to private link without touching a browser:
  </p>
  <pre class="content-codeblock"><code>my-report-tool | npx html-cloud -</code></pre>
</section>

<section class="content-section">
  <h2 class="content-h2">Options</h2>
  <table class="content-table">
    <thead>
      <tr><th>Option</th><th>Does</th><th>Default</th></tr>
    </thead>
    <tbody>
      <tr><td><code>--expires 7|30|never</code></td><td>Days until the link expires</td><td><code>30</code></td></tr>
      <tr><td><code>--no-copy</code></td><td>Don't copy the share link to the clipboard</td><td>copy is on</td></tr>
      <tr><td><code>--url &lt;base&gt;</code></td><td>Use a different server (or <code>$HTML_CLOUD_URL</code>)</td><td><code>https://html.cloud</code></td></tr>
    </tbody>
  </table>
  <p class="content-p">
    Limits: one <code>.html</code>/<code>.htm</code> file (or stdin), max 10&nbsp;MB, Node&nbsp;20+.
    Expiry can be changed later from the edit link. The clipboard is only touched in
    interactive use — piped and scripted runs never alter it.
  </p>
</section>

<section class="content-section">
  <h2 class="content-h2">FAQ</h2>
  <div class="faq">
    <div class="faq-item">
      <h3 class="content-h3">Do I need to install anything?</h3>
      <p class="content-p">No — <code>npx</code> (bundled with Node.js 20+) fetches and runs the tool in one step. Nothing is permanently installed.</p>
    </div>
    <div class="faq-item">
      <h3 class="content-h3">Where does the encryption happen?</h3>
      <p class="content-p">In the CLI process on your machine, before any upload. The package is <a href="https://www.npmjs.com/package/html-cloud" rel="noopener" target="_blank">on npm</a> and the source is <a href="https://github.com/viljamilaurila/html-cloud" rel="noopener" target="_blank">on GitHub</a> — it shares one crypto module with the web client, so you can verify this rather than trust it.</p>
    </div>
    <div class="faq-item">
      <h3 class="content-h3">Can I replace or delete a file I shared?</h3>
      <p class="content-p">Yes — open the edit link the command prints. You can replace the file without changing the share link, change the expiry, or delete it immediately.</p>
    </div>
  </div>
</section>
@endsection
