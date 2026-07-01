@extends('pages.content')

@section('title', 'html.cloud vs emailing an .html file — why a private link beats an attachment')
@section('description', 'Gmail and Outlook often block or flag .html attachments as a phishing risk, attachments bounce past a ~25 MB limit, and once sent a file lives in inboxes and backups forever with no expiry. html.cloud sends a private, browser-encrypted link that opens as a page and can expire or be replaced. A real scenario, the tradeoffs, and an FAQ.')
@section('og_title', 'html.cloud vs emailing an .html file')
@section('og_description', 'Email blocks or flags .html attachments, they bounce on size limits, and live in inboxes forever. html.cloud sends a private encrypted link that opens as a page and can expire. Here is how they differ.')
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
      "acceptedAnswer": { "@@type": "Answer", "text": "An .html file can contain scripts, so an attached HTML page is a classic phishing vector — mail providers like Gmail and Outlook routinely strip, quarantine, or warn the recipient about .html attachments. Sending a link instead sidesteps that entirely, and an html.cloud link opens the page directly in the browser." }
    },
    {
      "@@type": "Question",
      "name": "Is there a size limit on email attachments?",
      "acceptedAnswer": { "@@type": "Answer", "text": "Yes — Gmail caps attachments at about 25 MB and other providers are similar, so a larger HTML file with embedded images or data bounces or gets converted to a link anyway. html.cloud handles the file through a link from the start, up to 10 MB per file." }
    },
    {
      "@@type": "Question",
      "name": "Can I expire or update a file after emailing it?",
      "acceptedAnswer": { "@@type": "Answer", "text": "No. Once an attachment is sent it sits in the recipient's inbox, your sent folder, and every backup, with no expiry and no way to revoke it — and to change it you send a new email. With html.cloud you can set an expiry, delete the file, or replace it behind the same link so the recipient always opens the latest version." }
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
  Attaching an <code>.html</code> file to an email is the obvious move — and the one most likely to fail
  quietly. Mail providers treat HTML attachments as a phishing risk and often <em>block or flag</em>
  them, anything sizeable bounces on the attachment limit, and whatever gets through lives in inboxes and
  backups forever with no expiry. <strong>html.cloud</strong> sends a private, encrypted <em>link</em>
  instead — it opens as a page, and you can expire, replace, or delete it.
</p>

<section class="content-section">
  <h2 class="content-h2">Why the attachment quietly fails</h2>
  <p class="content-p">
    Because an HTML file can carry scripts, it's a textbook phishing vector, so Gmail and Outlook
    routinely strip <code>.html</code> attachments, drop them in spam, or slap a warning on them before
    the recipient can open — and none of that gives <em>you</em> a bounce message, so you often don't know
    it happened. If the file dodges the filters, it may still hit the ~25 MB attachment ceiling once it has
    embedded images or data. And the copy that does arrive is now permanent: it's in their inbox, your sent
    folder, and every backup, readable by anyone with mailbox access, with no way to pull it back or expire
    it. A link has none of those failure modes — html.cloud gives you one that opens the page directly,
    encrypted so only the people you send it to can read it.
  </p>
</section>

<section class="content-section">
  <h2 class="content-h2">At a glance</h2>
  <table class="content-table compare-table">
    <thead>
      <tr><th></th><th class="col-us">html.cloud</th><th>.html email attachment</th></tr>
    </thead>
    <tbody>
      <tr><td>Delivery</td><td class="col-us">A link that opens the page in the browser</td><td>A file the recipient must download and open</td></tr>
      <tr><td>Deliverability</td><td class="col-us">A normal link — nothing to strip</td><td>Often blocked, quarantined, or flagged as risky</td></tr>
      <tr><td>Size limit</td><td class="col-us">Up to 10&nbsp;MB via the link</td><td>Bounces past ~25&nbsp;MB on most providers</td></tr>
      <tr><td>Privacy</td><td class="col-us">Encrypted in your browser; key only in the link</td><td>Sits readable in inboxes, sent folders &amp; backups</td></tr>
      <tr><td>Expiry &amp; revocation</td><td class="col-us">7 / 30 days / never; delete anytime</td><td>None — once sent, it's out of your hands</td></tr>
      <tr><td>Updating it</td><td class="col-us">Replace behind the same link</td><td>Send a whole new email</td></tr>
    </tbody>
  </table>
</section>

<section class="content-section">
  <h2 class="content-h2">When emailing the file is genuinely fine</h2>
  <p class="content-p">
    This isn't always the wrong choice. If the file is small, non-sensitive, and one-off — and you know
    the recipient's provider won't strip it and you don't care that it lives in their inbox forever —
    attaching it is simple and needs no third-party service at all. html.cloud earns its place the moment
    any of that stops being true: the file is confidential, it needs to render correctly when opened, it's
    too big to attach, or it might change after you send it. Then a link you control beats a file you've
    let go of.
  </p>
</section>

<section class="content-section">
  <h2 class="content-h2">FAQ</h2>
  <div class="faq">
    <details class="faq-item">
      <summary>Why do HTML email attachments get blocked or flagged?</summary>
      <p>HTML files can contain scripts, so providers like Gmail and Outlook treat <code>.html</code>
        attachments as a phishing risk — stripping, quarantining, or warning before download. A link avoids
        that, and an html.cloud link opens the page directly in the browser.</p>
    </details>
    <details class="faq-item">
      <summary>Is there a size limit on email attachments?</summary>
      <p>Yes — Gmail caps attachments around 25&nbsp;MB and others are similar, so a larger HTML file with
        embedded images bounces or gets auto-converted to a link. html.cloud delivers via a link from the
        start, up to 10&nbsp;MB per file.</p>
    </details>
    <details class="faq-item">
      <summary>Can I expire or update a file after emailing it?</summary>
      <p>No — a sent attachment sits in inboxes and backups with no expiry or revocation, and changing it
        means a new email. With html.cloud you can expire it, delete it, or replace the file behind the same
        link so the recipient always sees the latest version.</p>
    </details>
  </div>
</section>

@include('partials.compare-links', ['current' => 'vs.email'])

<div class="content-cta">
  <a href="{{ route('home') }}" class="content-cta-btn">Go to the start page to share an HTML file →</a>
</div>
@endsection
