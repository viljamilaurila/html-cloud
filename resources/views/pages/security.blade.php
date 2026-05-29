@extends('pages.content')

@section('title', 'How html.cloud encryption works — zero-knowledge, client-side encrypted file sharing')
@section('description', 'How html.cloud encrypts HTML files in your browser with AES-256-GCM before upload. The decryption key lives in the URL fragment and never reaches our servers — we store only ciphertext. Plain-English summary plus the technical detail and an honest threat model.')
@section('og_title', 'How html.cloud encryption works')
@section('og_description', 'Zero-knowledge, client-side encryption explained: AES-256-GCM in your browser, the key in the URL fragment, and an honest threat model. Not even we can read your files.')
@section('canonical', config('app.url') . '/security')

@push('head')
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "TechArticle",
  "headline": "How html.cloud encryption works",
  "description": "A plain-English and technical explanation of html.cloud's zero-knowledge, client-side encryption model: AES-256-GCM in the browser, the decryption key in the URL fragment, and an honest threat model.",
  "url": "{{ config('app.url') }}/security",
  "publisher": { "@@type": "Organization", "name": "html.cloud", "url": "{{ config('app.url') }}" }
}
</script>
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "FAQPage",
  "mainEntity": [
    {
      "@@type": "Question",
      "name": "Is html.cloud really zero-knowledge?",
      "acceptedAnswer": { "@@type": "Answer", "text": "Yes. Your file is encrypted in your browser with AES-256-GCM before anything is uploaded. The decryption key lives in the part of the link after the # (the URL fragment), which browsers never send to a server. We store only the encrypted blob, so we cannot read your files." }
    },
    {
      "@@type": "Question",
      "name": "What can html.cloud see about my file?",
      "acceptedAnswer": { "@@type": "Answer", "text": "Only the ciphertext, its size in bytes, and the expiry you chose. We never receive the filename, the contents, or the decryption key." }
    },
    {
      "@@type": "Question",
      "name": "What happens if someone gets my link?",
      "acceptedAnswer": { "@@type": "Answer", "text": "Anyone who has the full link — including the part after the # — can read the file. The link is the credential. Treat it like a password: share it over a private channel and use an expiry for sensitive files." }
    },
    {
      "@@type": "Question",
      "name": "Do I need an account to share an encrypted HTML file?",
      "acceptedAnswer": { "@@type": "Answer", "text": "No. There is no sign-up. You drop a file, your browser encrypts it, and you get a link to share." }
    },
    {
      "@@type": "Question",
      "name": "Is html.cloud open source?",
      "acceptedAnswer": { "@@type": "Answer", "text": "Yes. The code is public on GitHub, so you can verify exactly how the encryption works and what the server stores rather than taking our word for it." }
    },
    {
      "@@type": "Question",
      "name": "Can I share confidential or company documents with html.cloud?",
      "acceptedAnswer": { "@@type": "Answer", "text": "Yes. Files are encrypted in your browser with AES-256-GCM before upload, and html.cloud only ever stores ciphertext — so even a server breach or a data request exposes nothing readable. Because the client is open source, your own team can review it before adopting it. Treat the link as the credential: share it over a trusted channel and set an expiry for sensitive files." }
    }
  ]
}
</script>
@endpush

@section('page')
<div class="content-eyebrow">Security</div>
<h1 class="content-title">How html.cloud encryption works</h1>

<p class="content-lead">
  Your HTML file is encrypted <em>in your browser</em> with AES-256-GCM before anything is
  uploaded. The decryption key lives in the part of the link after the <code class="accent">#</code> —
  which browsers never send to a server. We store only ciphertext, so not even we can read your files.
</p>

<section class="content-section">
  <h2 class="content-h2">The plain-English version</h2>
  <p class="content-p">
    When you drop an HTML file on html.cloud, the encryption happens on your own device before
    a single byte leaves it. Your browser generates a random key, scrambles the file with it,
    and uploads only the scrambled result. The key itself is tucked into the link you get back,
    after the <code class="accent">#</code> symbol.
  </p>
  <p class="content-p">
    That detail is the whole point. The text after a <code>#</code> in a URL is called the
    <em>fragment</em>, and browsers are built never to send it to the server — it stays in the
    address bar. So when someone opens your link, their browser fetches the encrypted blob from us,
    then uses the key from the fragment to decrypt it locally. We hand over scrambled bytes and never
    see the key needed to unscramble them. That is what “zero-knowledge” means here.
  </p>
</section>

<section class="content-section">
  <h2 class="content-h2">What we store — and what we can never see</h2>
  <div class="store-grid">
    <div class="store-col">
      <h3 class="content-h3">We store</h3>
      <ul class="content-list">
        <li>The encrypted blob (ciphertext + a random nonce)</li>
        <li>The size of the blob, in bytes</li>
        <li>The expiry you chose (7 days / 30 days / never)</li>
        <li>An auth hash so the owner can edit or delete</li>
      </ul>
    </div>
    <div class="store-col">
      <h3 class="content-h3">We never receive</h3>
      <ul class="content-list">
        <li>The file contents</li>
        <li>The decryption key</li>
        <li>The filename</li>
        <li>Anything that could decrypt the blob on its own</li>
      </ul>
    </div>
  </div>
</section>

<section class="content-section">
  <h2 class="content-h2">The technical detail</h2>
  <p class="content-p">
    For readers who want specifics, here is exactly what runs — all of it client-side, using the
    browser’s native <a href="https://developer.mozilla.org/en-US/docs/Web/API/Web_Crypto_API" rel="noopener" target="_blank">Web Crypto API</a>.
  </p>

  <h3 class="content-h3">Encryption</h3>
  <ul class="content-list">
    <li>The file is encrypted with
      <a href="https://en.wikipedia.org/wiki/Galois/Counter_Mode" rel="noopener" target="_blank"><strong>AES-256-GCM</strong></a> —
      a 256-bit key with an authenticated cipher mode that also detects tampering.</li>
    <li>A fresh <strong>96-bit (12-byte) random nonce</strong> is generated for every encryption
      via <code>crypto.getRandomValues</code>.</li>
    <li>The uploaded payload is the nonce prepended to the ciphertext, base64url-encoded for transport.</li>
  </ul>

  <h3 class="content-h3">Keys and the link</h3>
  <ul class="content-list">
    <li>The <strong>view key</strong> is a random AES-256 key generated with
      <code>crypto.subtle.generateKey</code>. It is exported and placed in the viewer link:
      <code>html.cloud/v/{id}#{key}</code>. It is the only thing that can decrypt your file, and it
      only ever exists in the fragment of that link.</li>
    <li>The <strong>edit key</strong> is 32 random bytes placed in a separate editor link:
      <code>html.cloud/e/{id}#{key}</code>. It lets you replace, re-expire, or delete the file.</li>
    <li>So the owner doesn’t need both keys, the view key is itself encrypted with the edit key and that
      encrypted copy is stored server-side. We also store <code>SHA-256(edit key)</code> as an auth
      token; edit and delete requests must prove they hold the edit key, and we compare hashes in
      constant time. The raw edit key never reaches us.</li>
  </ul>
  <p class="content-p">
    Because the keys live in URL fragments, they are never included in the HTTP request we receive —
    not in the path, not in headers. We genuinely cannot reconstruct them from anything on our servers.
  </p>
</section>

<section class="content-section">
  <h2 class="content-h2">Threat model &amp; honest limitations</h2>
  <p class="content-p">
    Encryption is only as strong as the assumptions around it. Here is what html.cloud does
    <em>not</em> protect against, stated plainly:
  </p>
  <ul class="content-list">
    <li><strong>The link is the credential.</strong> Anyone who obtains the full link — including the
      fragment — can read the file. There is no second factor. Share links over channels you trust, and
      use an expiry for sensitive files.</li>
    <li><strong>Link handling is your responsibility.</strong> A key in a URL can leak through browser
      history, screen-sharing, shoulder-surfing, or pasting the link into a tool that previews or
      crawls it. The fragment isn’t sent in the <code>Referer</code> header, but the link as a whole
      is still sensitive.</li>
    <li><strong>You trust us to serve honest code.</strong> The encryption runs in JavaScript we serve,
      so a compromised or malicious server could in principle ship code that leaks a key. This is the
      standard trust caveat for all browser-based encryption — and it is why html.cloud is
      <a href="https://github.com/viljamilaurila/html-cloud" rel="noopener" target="_blank">open source</a>:
      you can read exactly what runs in your browser and confirm it does what this page says.</li>
    <li><strong>No protection against a compromised device.</strong> If your machine or browser is
      compromised, the plaintext is readable before it is ever encrypted. Client-side encryption can’t
      help there.</li>
    <li><strong>Symmetric, no forward secrecy.</strong> One key encrypts the file for its whole life.
      If that key leaks, the content is exposed; rotating it means re-uploading.</li>
  </ul>
  <p class="content-p">
    What we can see is limited to metadata: the size of the encrypted blob, the expiry, upload and
    access timestamps, and request-level network information such as IP address at the moment of a
    request. We never see the contents, the filename, or the key.
  </p>
</section>

<section class="content-section">
  <h2 class="content-h2">Open source — don't take our word for it</h2>
  <p class="content-p">
    Every claim on this page is checkable, because the code that runs html.cloud is public. The
    browser-side encryption, the key handling, what the server stores — it is all there to read.
    If you are deciding whether to trust html.cloud with sensitive material, you don't have to
    rely on this page: read the source, or have your own engineers review it.
  </p>
  <ul class="content-list">
    <li>The client is the part that matters most for privacy — it does the encryption before
      anything is uploaded. You can read it line by line and verify the key never leaves your browser.</li>
    <li>You can also check exactly what the server receives and stores: ciphertext, a nonce, the
      blob size, your expiry, and an auth hash — and nothing that could decrypt your file.</li>
  </ul>
  <p class="content-p">
    <a class="source-link" href="https://github.com/viljamilaurila/html-cloud" rel="noopener" target="_blank">@include('partials.github-icon', ['size' => 16]) Read the source on GitHub →</a>
  </p>
</section>

<section class="content-section">
  <h2 class="content-h2">FAQ</h2>
  <div class="faq">
    <details class="faq-item">
      <summary>Is html.cloud really zero-knowledge?</summary>
      <p>Yes. Your file is encrypted in your browser with AES-256-GCM before anything is uploaded, and
        the decryption key stays in the URL fragment, which browsers never send to a server. We store
        only the encrypted blob.</p>
    </details>
    <details class="faq-item">
      <summary>What can html.cloud see about my file?</summary>
      <p>Only the ciphertext, its size in bytes, and the expiry you chose. We never receive the filename,
        the contents, or the decryption key.</p>
    </details>
    <details class="faq-item">
      <summary>What happens if someone gets my link?</summary>
      <p>Anyone with the full link — including the part after the <code>#</code> — can read the file. The
        link is the credential, so treat it like a password and use an expiry for sensitive files.</p>
    </details>
    <details class="faq-item">
      <summary>Do I need an account?</summary>
      <p>No. There is no sign-up. Drop a file, your browser encrypts it, and you get a link to share.</p>
    </details>
    <details class="faq-item">
      <summary>Is html.cloud open source?</summary>
      <p>Yes. The code is public on
        <a href="https://github.com/viljamilaurila/html-cloud" rel="noopener" target="_blank">GitHub</a>,
        so you can verify exactly how the encryption works and what the server stores rather than taking
        our word for it.</p>
    </details>
    <details class="faq-item">
      <summary>Can I share confidential or company documents with html.cloud?</summary>
      <p>That is what the design is for. Files are encrypted in your browser with AES-256-GCM before
        upload, and we only ever store ciphertext — so even a breach of our servers, or a request to us
        for your data, exposes nothing readable. Because the client is open source, your own team can
        review it before adopting it. Treat the link as the credential: share it over a channel you trust
        and set an expiry for sensitive files.</p>
    </details>
  </div>
</section>

<div class="content-cta">
  <a href="{{ route('home') }}" class="content-cta-btn">Go to the start page to share an HTML file →</a>
</div>
@endsection
