@extends('pages.content')

@section('title', 'Privacy policy — html.cloud browser extension')
@section('description', 'What the html.cloud browser extension does with your files: encrypted in your browser before upload, no personal data collected, no analytics, no third parties.')
@section('og_title', 'Privacy policy — html.cloud browser extension')
@section('og_description', 'Files are encrypted in your browser before upload. No personal data, no analytics, no third parties.')
@section('canonical', config('app.url') . '/extension-privacy')

@section('page')
<div class="content-eyebrow">Browser extension</div>
<h1 class="content-title">Privacy policy</h1>

<p class="content-lead">
  The html.cloud extension is built so that we <em>cannot</em> see the files you share.
  This page explains exactly what happens to your data. <span class="content-updated">Last updated 2026-08-27.</span>
</p>

<section class="content-section">
  <h2 class="content-h2">What the extension does with your files</h2>
  <p class="content-p">When you click <strong>Share to html.cloud</strong> on a local HTML file:</p>
  <ol class="content-list">
    <li>The file is read in your browser.</li>
    <li>It is encrypted in your browser with AES-256-GCM <strong>before anything is sent</strong>.</li>
    <li>Only the encrypted bytes (ciphertext) are uploaded to html.cloud.</li>
    <li>The decryption key is placed in the share link, after the <code>#</code>. Browsers never
        transmit the part of a URL after <code>#</code>, so the key never reaches our server.</li>
  </ol>
  <p class="content-p">
    Because of this design, html.cloud stores only ciphertext. We cannot read your file, and we
    cannot recover the key. Anyone you give the full link to can decrypt the file in their own
    browser; anyone with only our stored data cannot.
  </p>
</section>

<section class="content-section">
  <h2 class="content-h2">What is stored on your device</h2>
  <p class="content-p">
    The extension keeps a local record of files you’ve shared — the document id and its keys — in
    your browser’s storage. This lets those files appear under <strong>Your uploads</strong> on
    html.cloud. This record:
  </p>
  <ul class="content-list">
    <li>stays on your device,</li>
    <li>is never transmitted to any server,</li>
    <li>can be cleared at any time by removing the extension or clearing site data.</li>
  </ul>
</section>

<section class="content-section">
  <h2 class="content-h2">What we collect</h2>
  <ul class="content-list">
    <li><strong>Personal information:</strong> none.</li>
    <li><strong>Analytics or tracking:</strong> none.</li>
    <li><strong>Third-party services:</strong> none. The extension communicates only with
        html.cloud, and only to upload the ciphertext you chose to share.</li>
  </ul>
  <p class="content-p">
    We do not sell or share data with third parties, and we do not use your data for any purpose
    unrelated to sharing the file you asked us to share.
  </p>
</section>

<section class="content-section">
  <h2 class="content-h2">Permissions</h2>
  <ul class="content-list">
    <li><strong>File access</strong> — so the Share button can appear on local <code>.html</code>
        files. The file is read only when you click Share.</li>
    <li><strong>html.cloud host access</strong> — to upload the encrypted file and open your link.</li>
    <li><strong>Storage</strong> — to remember your own shares locally.</li>
    <li><strong>Clipboard</strong> — to copy the share link after a successful share.</li>
  </ul>
</section>

<section class="content-section">
  <h2 class="content-h2">Contact</h2>
  <p class="content-p">
    Questions: open an issue at
    <a href="https://github.com/viljamilaurila/html-cloud" rel="noopener" target="_blank">github.com/viljamilaurila/html-cloud</a>
    or email the address listed on <a href="{{ route('home') }}">html.cloud</a>.
  </p>
</section>
@endsection
