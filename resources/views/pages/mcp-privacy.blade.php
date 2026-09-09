@extends('pages.content')

@section('title', 'Privacy policy — html.cloud for Claude Desktop')
@section('description', 'What the html.cloud Claude Desktop extension (MCP server) does with your data: HTML is encrypted on your computer before upload, only ciphertext is sent, no personal data, no analytics, no third parties.')
@section('og_title', 'Privacy policy — html.cloud for Claude Desktop')
@section('og_description', 'HTML is encrypted on your computer before upload. Only ciphertext is sent. No personal data, no analytics, no third parties.')
@section('canonical', config('app.url') . '/mcp-privacy')

@section('page')
<div class="content-eyebrow">Claude Desktop extension · MCP server</div>
<h1 class="content-title">Privacy policy</h1>
<p class="content-lead">
  The html.cloud extension for Claude Desktop (the <code>html-cloud-mcp</code> server) is built so
  that we <em>cannot</em> see the pages Claude shares through it. This page explains exactly what
  it does with your data. <span class="content-updated">Last updated 2026-09-09.</span>
</p>

<section class="content-section">
  <h2 class="content-h2">What we collect</h2>
  <p class="content-p">
    The extension handles only the HTML that Claude passes to its two tools, <code>share_html</code>
    and <code>update_html</code>, when you ask for a page to be shared or changed. It does not read
    files on your computer, your conversation, or anything else from Claude. It collects:
  </p>
  <ul class="content-list">
    <li><strong>Personal information:</strong> none. No account, no email, no name.</li>
    <li><strong>Analytics or tracking:</strong> none. No telemetry, no crash reporting.</li>
    <li><strong>Third-party services:</strong> none. It communicates only with html.cloud.</li>
  </ul>
</section>

<section class="content-section">
  <h2 class="content-h2">How your data is used and stored</h2>
  <p class="content-p">When Claude shares a page:</p>
  <ol class="content-list">
    <li>The extension encrypts the HTML on your computer with AES-256-GCM <strong>before anything is sent</strong>.</li>
    <li>What is uploaded to html.cloud: the encrypted bytes (ciphertext), the expiry you chose, a copy
        of the page key that is itself encrypted with the edit key, and a hash of the edit key so the
        server can check that later updates are authorised. None of these let us read the page.</li>
    <li>The decryption key is placed in the share link, after the <code>#</code>. Browsers never
        transmit the part of a URL after <code>#</code>, so the key never reaches our server.</li>
  </ol>
  <p class="content-p">
    html.cloud stores those values. We cannot read the page and cannot recover either key. When
    Claude updates a page, the new HTML is encrypted under the same page key on your computer, and
    only the new ciphertext, the page id, and the edit-key proof are sent. The share and edit links are returned to Claude in the
    conversation; nothing is stored on your computer by the extension itself.
  </p>
  <p class="content-p">
    Our servers keep ordinary short-lived request logs (such as IP address and timestamp) to
    operate the service and prevent abuse, as any web server does. These are not linked to the
    content of a page, which we cannot read.
  </p>
</section>

<section class="content-section">
  <h2 class="content-h2">Sharing with third parties</h2>
  <p class="content-p">
    We do not sell, share, or transfer data to third parties. The only people who can read a page
    are those you give the full link to; they decrypt it in their own browser.
  </p>
</section>

<section class="content-section">
  <h2 class="content-h2">Retention</h2>
  <p class="content-p">
    A page becomes unavailable the moment its link expires — 7 or 30 days after sharing, or never
    if you chose a permanent link — and the stored ciphertext is permanently deleted by a daily
    clean-up. Deleting a page through its private edit link removes it immediately.
  </p>
</section>

<section class="content-section">
  <h2 class="content-h2">Contact</h2>
  <p class="content-p">
    Questions or requests: email <a href="mailto:viljami@liito.io">viljami@liito.io</a> or open an
    issue at <a href="https://github.com/viljamilaurila/html-cloud/issues" rel="noopener" target="_blank">github.com/viljamilaurila/html-cloud</a>.
    The extension's source code is in the <code>mcp/</code> directory of that repository.
  </p>
</section>
@endsection
