@extends('pages.content')

@section('title', 'html.cloud vs emailing an .html file — why a private link beats an attachment')
@section('description', 'html.cloud vs sending an .html attachment: mail providers often block or flag HTML attachments, recipients must download and trust them, and there is no expiry or revocation. html.cloud sends a private, client-side-encrypted link that opens as a page. Comparison, when to use each, and an FAQ.')
@section('og_title', 'html.cloud vs emailing an .html file')
@section('og_description', 'Email often blocks or flags .html attachments, and they live in inboxes forever. html.cloud sends a private encrypted link that opens as a page and can expire. Here is how they differ.')
@section('canonical', config('app.url') . '/vs/email-attachment')

@push('head')
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "FAQPage",
  "mainEntity": [
    {
      "@@type": "Question",
      "name": "Why do HTML email attachments get blocked or flagged?",
      "acceptedAnswer": { "@@type": "Answer", "text": "HTML files can contain scripts, so mail providers often treat .html attachments as a phishing risk — stripping them, quarantining them, or warning the recipient before they download. Sharing a link instead of an attachment avoids that, and with html.cloud the link opens the page directly in the browser." }
    },
    {
      "@@type": "Question",
      "name": "Is an email attachment private?",
      "acceptedAnswer": { "@@type": "Answer", "text": "Not especially. The file sits in the recipient's inbox and your sent folder, gets copied into backups, and is readable by anyone with access to those mailboxes — with no expiry or way to revoke it. html.cloud encrypts the file in your browser, carries the key in the link, and lets you set an expiry or delete it." }
    },
    {
      "@@type": "Question",
      "name": "Can I update a file after sending it?",
      "acceptedAnswer": { "@@type": "Answer", "text": "Once an attachment is sent, it's gone — you'd have to send a new email. With html.cloud you can replace the file behind the same link, so the recipient always opens the latest version." }
    }
  ]
}
</script>
@endpush

@section('page')
<div class="content-eyebrow">Comparison</div>
<h1 class="content-title">html.cloud vs emailing an .html file</h1>

@include('partials.vs-hero', [
  'glyphs' => ['email'],
  'label'  => 'Email attachment',
])

<p class="content-lead">
  Email often <em>blocks or flags</em> .html attachments, the file then lives in inboxes and backups
  forever, and there's no expiry or update. <strong>html.cloud</strong> sends a private, encrypted
  <em>link</em> that opens as a page — and you can expire, replace, or delete it.
</p>

<section class="content-section">
  <h2 class="content-h2">At a glance</h2>
  <table class="content-table compare-table">
    <thead>
      <tr><th></th><th class="col-us">html.cloud</th><th>.html email attachment</th></tr>
    </thead>
    <tbody>
      <tr><td>Delivery</td><td class="col-us">A link that opens the page in the browser</td><td>A file the recipient must download and open</td></tr>
      <tr><td>Deliverability</td><td class="col-us">A normal link — nothing to strip</td><td>Often blocked, quarantined, or flagged as risky</td></tr>
      <tr><td>Privacy</td><td class="col-us">Encrypted in your browser; key only in the link</td><td>Sits readable in inboxes, sent folders &amp; backups</td></tr>
      <tr><td>Expiry &amp; revocation</td><td class="col-us">7 / 30 days / never; delete anytime</td><td>None — once sent, it's out of your hands</td></tr>
      <tr><td>Updating it</td><td class="col-us">Replace behind the same link</td><td>Send a whole new email</td></tr>
      <tr><td>Large files</td><td class="col-us">Up to 10&nbsp;MB via the link</td><td>Often bounce on attachment-size limits</td></tr>
    </tbody>
  </table>
</section>

<section class="content-section">
  <h2 class="content-h2">When to use which</h2>
  <p class="content-p">
    <strong>Emailing the file</strong> is fine for something small, non-sensitive, and one-off, when you
    know the recipient's mail provider won't block it and you don't care that it lives in their inbox
    indefinitely. It needs no third-party service to view.
  </p>
  <p class="content-p">
    <strong>Use html.cloud</strong> when the file is sensitive, needs to look right when opened, or might
    change — a client report, a proposal, an AI-generated presentation. You send a link instead of an
    attachment, it's encrypted before it leaves your device, and you stay in control of expiry and updates.
  </p>
</section>

<section class="content-section">
  <h2 class="content-h2">FAQ</h2>
  <div class="faq">
    <details class="faq-item">
      <summary>Why do HTML email attachments get blocked or flagged?</summary>
      <p>HTML files can contain scripts, so providers often treat .html attachments as a phishing risk —
        stripping, quarantining, or warning before download. A link avoids that, and an html.cloud link
        opens the page directly in the browser.</p>
    </details>
    <details class="faq-item">
      <summary>Is an email attachment private?</summary>
      <p>Not especially — it sits in the recipient's inbox and your sent folder, gets backed up, and can't
        be expired or revoked. html.cloud encrypts the file in your browser, keeps the key in the link, and
        lets you set an expiry or delete it.</p>
    </details>
    <details class="faq-item">
      <summary>Can I update a file after sending it?</summary>
      <p>A sent attachment is final. With html.cloud you can replace the file behind the same link, so the
        recipient always sees the latest version.</p>
    </details>
  </div>
</section>

@include('partials.compare-links', ['current' => 'vs.email'])

<div class="content-cta">
  <a href="{{ route('home') }}" class="content-cta-btn">Go to the start page to share an HTML file →</a>
</div>
@endsection
