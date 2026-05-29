{{-- Short zero-knowledge explainer with a link to the full security page. --}}
<section class="content-section">
  <h2 class="content-h2">Private by design</h2>
  <p class="content-p">
    The encryption happens in your browser before anything is uploaded, so we only ever store
    ciphertext — never the file, the filename, or the key. The decryption key lives in the link, which
    browsers never send to a server. Not even we can read your files.
    <a href="{{ route('security') }}">Read how the encryption works →</a>
  </p>
</section>
