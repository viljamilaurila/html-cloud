@extends('pages.content')

@section('title', 'Claude artifacts vs html.cloud — when to publish, when to share privately')
@section('description', 'Claude artifacts and html.cloud are not rivals. Keep using artifacts while you work and publish them when the page is meant to be public. Switch to html.cloud when the page holds sensitive material or has to reach someone outside your organisation: it is encrypted in your browser and travels as a private link. A simple rule, a comparison table, and an FAQ.')
@section('og_title', 'Claude artifacts vs html.cloud')
@section('og_description', 'They do not compete. Artifacts for working and publishing; html.cloud for private, encrypted delivery of anything sensitive or leaving your organisation.')
@section('canonical', config('app.url') . '/vs/claude-artifacts')

@push('head')
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "FAQPage",
  "mainEntity": [
    {
      "@@type": "Question",
      "name": "Should I stop using Claude artifacts and use html.cloud instead?",
      "acceptedAnswer": { "@@type": "Answer", "text": "No. Artifacts are the right place to build and iterate on a page inside Claude, and publishing one is fine for anything meant to be public. html.cloud is for the moment a page has to leave that context: when it contains sensitive material, or has to reach someone outside your organisation without being put on a public URL. Most people use both." }
    },
    {
      "@@type": "Question",
      "name": "What happens when I publish a Claude artifact?",
      "acceptedAnswer": { "@@type": "Answer", "text": "Publishing puts the artifact at a public link that anyone who has the address can open, and the page is stored in readable form so it can be served. That is exactly right for a public demo or a page you would happily post anywhere. It is the wrong default for a client report, internal figures, or anything with personal data in it." }
    },
    {
      "@@type": "Question",
      "name": "How does Claude know when to use html.cloud?",
      "acceptedAnswer": { "@@type": "Answer", "text": "With the html.cloud extension installed, Claude is told to use html.cloud for sensitive, confidential, or client material and for anything going to people outside your organisation, and to keep using its own artifacts for casual pages. If you would rather never publish an artifact, an install-time switch makes html.cloud the default for every share." }
    }
  ]
}
</script>
@endpush

@section('page')
<div class="content-eyebrow">Comparison</div>
<h1 class="content-title">Claude artifacts vs html.cloud</h1>

@include('partials.vs-hero', ['glyphs' => [], 'label' => 'Claude artifacts'])

<p class="content-lead">
  This one isn't a contest. <strong>Claude artifacts</strong> are where a page gets <em>made</em>:
  you iterate on it in the chat, and when it's meant for the world you publish it. <strong>html.cloud</strong>
  is for the moment a page has to <em>leave</em> that context and shouldn't become public — a client
  deliverable, internal numbers, anything with personal data in it. It's encrypted in your browser and
  travels as a private link. Keep using artifacts by default; reach for html.cloud when the content is
  sensitive or the reader is outside your organisation.
</p>

<section class="content-section">
  <h2 class="content-h2">A simple rule</h2>
  <p class="content-p">
    Keep using artifacts by default. html.cloud steps in when the page is sensitive or has to
    reach someone outside your organisation — and you can make it the default for everything
    with one switch when you install the extension.
  </p>
  @include('partials.route-demo')
</section>

<section class="content-section">
  <h2 class="content-h2">What "publish" actually does</h2>
  <p class="content-p">
    Publishing an artifact puts it at a public link. Anyone who has that address can open it, whether
    you sent it to them or it was forwarded, pasted into a group chat, or found later — and the page is
    stored in readable form so it can be served. None of that is a flaw; it's what public means, and it's
    exactly right for a page you'd happily put on a billboard. It only becomes a problem when the page
    was never meant to be public, and you published it because it was the easiest way to hand it to
    someone who isn't in your Claude workspace.
  </p>
  <p class="content-p">
    That gap is what html.cloud fills. The page is encrypted with AES-256-GCM in your browser (or by the
    Claude extension, on your computer) before anything is uploaded. The key rides after the
    <code class="accent">#</code> in the link, which browsers never send to servers, so we store only
    ciphertext we cannot read. The recipient opens the link like any web page — no account, no Claude,
    nothing to install. When you're done, set it to expire or delete it.
  </p>
</section>

<section class="content-section">
  <h2 class="content-h2">At a glance</h2>
  <table class="content-table compare-table">
    <thead>
      <tr><th></th><th class="col-us">html.cloud</th><th>Claude artifacts</th></tr>
    </thead>
    <tbody>
      <tr><td>Best at</td><td class="col-us">Delivering a finished page privately</td><td>Building and iterating on the page inside the chat</td></tr>
      <tr><td>Sharing outside your organisation</td><td class="col-us">Private link, encrypted; no account for the reader</td><td>Publish to a public link</td></tr>
      <tr><td>Who can open a shared page</td><td class="col-us">Only people who have the exact link</td><td>Anyone with the public link, or workspace members for internal sharing</td></tr>
      <tr><td>How the page is stored</td><td class="col-us">Ciphertext only; we can't read it</td><td>Readable, so it can be served</td></tr>
      <tr><td>Changing it after sharing</td><td class="col-us">Yes — same link shows the new version</td><td>Yes — republish</td></tr>
      <tr><td>Expiry &amp; deletion</td><td class="col-us">7 / 30 days / never; delete anytime</td><td>Unpublish anytime</td></tr>
      <tr><td>Lives inside the Claude conversation</td><td class="col-us">No — it's a link you send on</td><td>Yes, that's the point of it</td></tr>
    </tbody>
  </table>
</section>

<section class="content-section">
  <h2 class="content-h2">How Claude picks, once it's connected</h2>
  <p class="content-p">
    With the <a href="{{ route('mcp') }}">html.cloud extension</a> installed, Claude gets a standing
    instruction: use html.cloud for sensitive, confidential, or client material and for anything going
    to people outside your organisation; keep using artifacts for casual pages; ask when unsure. So you
    don't have to think about it — say "share this with the client" and a private link comes back; say
    "show me a quick mock-up" and you get an artifact as usual. If you'd rather never publish an artifact,
    an install-time switch, <em>Always share through html.cloud</em>, makes it the default for every share.
  </p>
</section>

<section class="content-section">
  <h2 class="content-h2">FAQ</h2>
  <div class="faq">
    <details class="faq-item">
      <summary>Should I stop using Claude artifacts and use html.cloud instead?</summary>
      <p>No. Artifacts are the right place to build and iterate on a page inside Claude, and publishing one
        is fine for anything meant to be public. html.cloud is for when a page has to leave that context
        without becoming public. Most people use both.</p>
    </details>
    <details class="faq-item">
      <summary>What happens when I publish a Claude artifact?</summary>
      <p>It gets a public link anyone can open, and the page is stored in readable form so it can be served.
        Right for a demo or a page you'd post anywhere; the wrong default for a client report, internal
        figures, or anything with personal data.</p>
    </details>
    <details class="faq-item">
      <summary>How does Claude know when to use html.cloud?</summary>
      <p>The extension tells it: html.cloud for sensitive material and anything leaving your organisation,
        artifacts for casual pages. A switch at install time makes html.cloud the default for everything
        if you prefer.</p>
    </details>
  </div>
</section>

@include('partials.compare-links', ['current' => 'vs.artifacts'])

<div class="content-cta">
  <a href="{{ route('mcp') }}" class="content-cta-btn">Connect html.cloud to Claude →</a>
</div>
@endsection
