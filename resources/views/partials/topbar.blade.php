<header class="topbar">
  <a href="/" class="wordmark">
    @include('partials.cloud-mark', ['size' => 22])
    <span class="wordmark-text"><span class="wordmark-html">html</span><span class="wordmark-cloud">.cloud</span></span>
  </a>
  <div class="topbar-right">
    <span class="topbar-tagline">{{ $tagline ?? 'encrypted in your browser' }}</span>
    <a href="https://github.com/viljamilaurila/html-cloud" class="topbar-github" rel="noopener" target="_blank" title="html.cloud is open source — read the code on GitHub">
      @include('partials.github-icon', ['size' => 16])
      <span class="topbar-github-label">Open source</span>
    </a>
  </div>
</header>
