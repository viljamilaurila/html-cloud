@extends('layout')
@section('title', 'html.cloud')
@section('robots', 'noindex, nofollow')

@section('content')
<div id="loading-screen" class="loading-screen">
  <div class="loading-inner">
    <div class="loading-spinner"></div>
    <span class="loading-text">Decrypting…</span>
  </div>
</div>

<div id="error-screen" class="error-screen hidden">
  <div class="error-inner">
    <div class="error-icon">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></svg>
    </div>
    <h2 class="error-title" id="error-title">Unable to decrypt</h2>
    <p class="error-body" id="error-body">The link may be incomplete or the file has been removed.</p>
    <a href="/" class="error-link">← Back to html.cloud</a>
  </div>
</div>

<iframe id="content-frame" class="content-frame hidden" sandbox="allow-scripts allow-popups" title="Shared HTML file"></iframe>

<script>
  window.__DOC_ID__ = @json($doc->id);
</script>
@endsection

@push('scripts')
@vite('resources/js/viewer.js')</script>
@endpush
