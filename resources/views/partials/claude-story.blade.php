{{-- Homepage storyboard: a Claude chat makes a page, shares it privately, the
     recipient opens it, then an update lands behind the same link. Steps are
     revealed by claude-story.js (data-at = the step an element appears on);
     without JS or with reduced motion the finished conversation shows. --}}
<section class="cs" aria-labelledby="cs-title">
  <div class="cs-copy">
    <p class="cs-eyebrow">
      <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 1c1 5 3 7 8 8-5 1-7 3-8 8-1-5-3-7-8-8 5-1 7-3 8-8z"/></svg>
      Works inside Claude
    </p>
    <h2 class="cs-title" id="cs-title">Made it with Claude? Ask Claude to share it.</h2>
    <p class="cs-lead">
      Claude encrypts the page on your computer and hands you a private link. Ask for a
      change later and the same link shows the new version — nothing to re-send.
    </p>
    <ol class="cs-phases" aria-label="How it goes">
      <li data-phase="1">Make it with Claude</li>
      <li data-phase="2">Ask Claude to share it</li>
      <li data-phase="3">They open the link</li>
      <li data-phase="4">Update, same link</li>
    </ol>
    <a class="cs-cta" href="{{ route('mcp') }}">Add html.cloud to Claude →</a>
    <p class="cs-cta-sub">Claude Desktop and Claude Code · free · no account</p>
  </div>

  <div class="cs-stage is-static" id="claude-story" data-step="7" aria-hidden="true">
    <div class="cs-chat">
      <div class="cs-chat-head"><span class="cs-chat-dot"></span>Claude</div>
      <div class="cs-chat-body">
        <div class="cs-msg cs-you" data-at="1">Make a one-page Q3 report for the board.</div>
        <div class="cs-msg cs-claude" data-at="2">
          <span class="cs-typing"><i></i><i></i><i></i></span>
          <div class="cs-reply">
            Here’s the report.
            <span class="cs-file">
              <span class="cs-file-icon">&lt;/&gt;</span>
              <span><b>Q3 report</b><small>HTML · 1 page</small></span>
            </span>
          </div>
        </div>
        <div class="cs-msg cs-you" data-at="3">Share it privately so I can send it to the board.</div>
        <div class="cs-msg cs-claude" data-at="4">
          <span class="cs-typing"><i></i><i></i><i></i></span>
          <div class="cs-reply">
            <span class="cs-locked">
              <svg width="12" height="12" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="7" width="10" height="8" rx="1.5"/><path d="M5 7V5a3 3 0 0 1 6 0v2"/></svg>
              Encrypted on your computer
            </span>
            Anyone with this link can open it:
            <span class="cs-link">html.cloud/v/kT4eN7xQ/q3-report#b3Fv…</span>
          </div>
        </div>
        <div class="cs-msg cs-you" data-at="6">Add a short “next steps” section at the end.</div>
        <div class="cs-msg cs-claude" data-at="7">
          <span class="cs-typing"><i></i><i></i><i></i></span>
          <div class="cs-reply">Done — updated in place. <span class="cs-tag">same link</span></div>
        </div>
      </div>
    </div>

    <div class="cs-browser" data-at="5">
      <div class="cs-browser-bar">
        <span class="cs-browser-dots"><i></i><i></i><i></i></span>
        <span class="cs-address">
          <svg width="10" height="10" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="7" width="10" height="8" rx="1.5"/><path d="M5 7V5a3 3 0 0 1 6 0v2"/></svg>
          html.cloud/v/kT4eN7xQ/q3-report
        </span>
      </div>
      <div class="cs-page">
        <span class="cs-page-kicker">Board update</span>
        <span class="cs-page-title">Q3 at a glance</span>
        <span class="cs-bars"><i style="--h:42%"></i><i style="--h:58%"></i><i style="--h:51%"></i><i style="--h:76%"></i><i style="--h:88%"></i></span>
        <span class="cs-line"></span><span class="cs-line cs-line-short"></span>
        <span class="cs-next" data-at="7"><b>Next steps</b><span class="cs-line"></span><span class="cs-line cs-line-short"></span></span>
      </div>
      <span class="cs-viewer-note">What the board sees · no account needed</span>
    </div>
  </div>

  <div class="cs-why">
    <h3 class="cs-why-title">Why not just publish the artifact?</h3>
    <p class="cs-why-lead">Publishing is right for pages meant for everyone. For the ones that aren’t:</p>
    <ul class="cs-why-list">
      <li>
        <b>Encrypted, not just private</b>
        A shared artifact lives on Anthropic’s servers as a normal page. html.cloud only ever
        holds ciphertext — not even we can read it.
      </li>
      <li>
        <b>No Claude account to open it</b>
        Outside your organisation, an artifact needs a public link (an admin setting on Team and
        Enterprise) or a Claude account for each reader. An html.cloud link opens in any browser.
      </li>
      <li>
        <b>Gone when you say so</b>
        Artifact links stay up until you unshare them. html.cloud links can expire after 7 or
        30 days — or never — and you can delete one anytime.
      </li>
    </ul>
    <a class="cs-why-more" href="{{ route('vs.artifacts') }}">Claude artifacts vs html.cloud, in detail →</a>
  </div>
</section>
