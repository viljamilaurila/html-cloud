@extends('layout')
@section('title', 'File not found — html.cloud')
@section('robots', 'noindex, nofollow')

@section('content')
<div class="shell">
  <header class="topbar">
    <a href="/" class="wordmark">
      <span class="wordmark-html">html</span><span class="wordmark-cloud">.cloud</span>
    </a>
    <span class="topbar-tagline">encrypted in your browser</span>
  </header>

  <main style="display:flex;flex-direction:column;align-items:center;padding:80px 56px;">
    <div class="error-inner">
      <div class="error-icon">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/>
        </svg>
      </div>
      <h2 class="error-title">This file is gone</h2>
      <p class="error-body">It may have expired or been removed by its owner.</p>
      <a href="/" class="error-link">← Share a new file</a>
    </div>
  </main>
</div>
@endsection
