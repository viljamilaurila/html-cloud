<header class="topbar">
  <a href="/" class="wordmark">
    @include('partials.cloud-mark', ['size' => 22])
    <span class="wordmark-text"><span class="wordmark-html">html</span><span class="wordmark-cloud">.cloud</span></span>
  </a>
  <span class="topbar-tagline">{{ $tagline ?? 'encrypted in your browser' }}</span>
</header>
