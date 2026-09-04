<header class="topbar">
  <a href="/" class="wordmark" aria-label="html.cloud"></a>
  <div class="topbar-right">
    <a href="{{ route('uploads') }}" class="topbar-link">Your uploads</a>
    <span class="topbar-tagline">{{ $tagline ?? 'encrypted in your browser' }}</span>
    <a href="https://github.com/viljamilaurila/html-cloud" class="topbar-github" rel="noopener" target="_blank" title="html.cloud is open source — read the code on GitHub">
      @include('partials.github-icon', ['size' => 16])
      <span class="topbar-github-label">Open source</span>
    </a>
  </div>
</header>
