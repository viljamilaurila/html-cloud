{{-- Homepage "Under the hood" band: the technical version of the privacy
     promise, for people who want to check it rather than take it on trust. --}}
<section class="uth" aria-labelledby="uth-title">
  <p class="uth-eyebrow">// under the hood</p>
  <h2 class="uth-title" id="uth-title">The key lives in the link. Never on our server.</h2>
  <p class="uth-lead">
    Your browser encrypts the file before upload, and the only key goes after the
    <code>#</code> — the one part of a URL browsers never send to a server.
  </p>

  {{-- Shredder animation, driven by uth-anim.js. Without JS / with reduced
       motion it shows the end state: both browsers readable, the server holding
       only scrambled bytes, the key delivered beside it rather than through it. --}}
  <div class="uth-anim is-static" id="uth-anim" aria-hidden="true">
    <div class="uth-node uth-node-you">
      <span class="uth-node-label">Your browser</span>
      <pre class="uth-doc" data-doc="you"></pre>
    </div>
    <div class="uth-node uth-node-server">
      <span class="uth-node-label">html.cloud server</span>
      <pre class="uth-doc uth-doc-server" data-doc="server"></pre>
      <span class="uth-server-meta">keys stored: <b>0</b></span>
    </div>
    <div class="uth-node uth-node-them">
      <span class="uth-node-label">Their browser</span>
      <pre class="uth-doc" data-doc="them"></pre>
    </div>
    <div class="uth-track"><span>the link you send · key after #</span></div>
    <span class="uth-keychip">#b3Fv… key</span>
    <pre class="uth-packet"></pre>
  </div>
  <ol class="uth-steps" id="uth-steps">
    <li data-step="1">Your browser shreds the file with a fresh 256-bit key</li>
    <li data-step="2">Only the scrambled bytes are uploaded</li>
    <li data-step="3">The key travels in the link you send — never through us</li>
    <li data-step="4">Their browser fetches the bytes and unlocks them with the key</li>
  </ol>

  <figure class="uth-url" aria-label="Anatomy of a share link">
    <div class="uth-url-line">
      <span class="uth-seg uth-seg-host">https://html.cloud</span><span class="uth-seg uth-seg-id">/v/kT4eN7xQ</span><span class="uth-seg uth-seg-name">/q3-report</span><span class="uth-seg uth-seg-key">#b3FvXy9Lm2…Qe2k</span>
    </div>
    <ol class="uth-url-notes">
      <li class="uth-note-id"><b>/v/kT4eN7xQ</b> which file — all we can look up is its ciphertext</li>
      <li class="uth-note-name"><b>/q3-report</b> optional readable name, purely cosmetic</li>
      <li class="uth-note-key"><b>#b3Fv…</b> the 256-bit key — stays in the browser, never reaches us</li>
    </ol>
  </figure>

  <pre class="uth-code" aria-label="What happens when you share a file"><code><span class="uth-c">// in your browser — nothing below leaves it unencrypted</span>
<span class="uth-k">const</span> key  = crypto.getRandomValues(<span class="uth-k">new</span> Uint8Array(<span class="uth-n">32</span>))  <span class="uth-c">// 256-bit</span>
<span class="uth-k">const</span> blob = AES_256_GCM.encrypt(file, key)              <span class="uth-c">// Web Crypto, no libraries</span>

POST /api/documents { blob, editHash }                   <span class="uth-c">// nothing readable</span>
<span class="uth-k">return</span> `html.cloud/v/${id}#${key}`                       <span class="uth-c">// the key rides after #</span></code></pre>

  <div class="uth-links">
    <a href="{{ route('security') }}">How the encryption works →</a>
    <a href="{{ route('security') }}#threat-model">What it doesn’t protect against →</a>
    <a href="https://github.com/viljamilaurila/html-cloud" rel="noopener" target="_blank">Read the source on GitHub ↗</a>
  </div>
</section>
