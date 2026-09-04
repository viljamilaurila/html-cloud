<footer class="site-footer">
  <div class="footer-inner">
    @if ($showAbout ?? true)
    <p class="footer-about">
      html.cloud is a private HTML file sharing service. Drop any HTML file and get a shareable link —
      your browser encrypts the content with AES-256-GCM before anything leaves your device.
      The server stores only ciphertext. Not even we can read your files.
    </p>
    @endif
    <p class="footer-uses">
      Built for sharing HTML presentations, reports, internal documents, and prototypes.
      Files can be set to expire automatically, replaced without changing the link, or deleted on demand.
    </p>
    <div class="footer-links">
      <nav class="footer-links-col">
        <span class="footer-links-label">Use cases</span>
        <a href="{{ route('use.claude') }}">Share a Claude artifact</a>
        <a href="{{ route('use.presentation') }}">Share an AI presentation</a>
        <a href="{{ route('use.report') }}">Send a client a private report</a>
        <a href="{{ route('use.internal') }}">Share an internal document</a>
      </nav>
      <nav class="footer-links-col">
        <span class="footer-links-label">Compare</span>
        <a href="{{ route('vs.netlify') }}">vs Netlify Drop</a>
        <a href="{{ route('vs.codepen') }}">vs CodePen</a>
        <a href="{{ route('vs.drive') }}">vs Google Drive &amp; Dropbox</a>
        <a href="{{ route('vs.email') }}">vs emailing an .html file</a>
      </nav>
      <nav class="footer-links-col">
        <span class="footer-links-label">More</span>
        <a href="{{ route('home') }}">Home</a>
        <a href="{{ route('uploads') }}">Uploaded from this device</a>
        <a href="{{ route('security') }}">How encryption works</a>
        <a href="{{ route('cli') }}">CLI — share from the terminal</a>
        <a href="{{ route('mcp') }}">Connect to Claude (MCP)</a>
        <a href="https://github.com/viljamilaurila/html-cloud" rel="noopener" target="_blank">Source on GitHub ↗</a>
      </nav>
    </div>
    <p class="footer-copy">
      &copy; {{ date('Y') }} html.cloud
      @if ($documentCount)
        &nbsp;&middot;&nbsp; {{ number_format($documentCount) }} files encrypted and shared
      @endif
    </p>
  </div>
</footer>
