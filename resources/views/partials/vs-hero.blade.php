{{-- Comparison hero: html.cloud vs competitor, with brand glyphs and outbound links.
     Usage:
       @include('partials.vs-hero', [
         'glyphs' => ['googledrive', 'dropbox'],
         'links'  => [['label' => 'Google Drive', 'url' => 'https://drive.google.com'],
                      ['label' => 'Dropbox', 'url' => 'https://www.dropbox.com']],
       ])
     For a non-service competitor (e.g. email), pass 'label' instead of 'links'. --}}
@php
$links = $links ?? [];
@endphp
<div class="vs-hero">
  <div class="vs-card vs-card-us">
    @include('partials.cloud-mark', ['size' => 26])
    <span class="vs-name">html.cloud</span>
  </div>
  <span class="vs-divider" aria-hidden="true">vs</span>
  <div class="vs-card">
    <span class="vs-glyphs">
      @foreach ($glyphs as $g)
        @include('partials.brand-icon', ['brand' => $g, 'size' => 26])
      @endforeach
    </span>
    <span class="vs-name">
      @if (count($links))
        @foreach ($links as $i => $l)@if ($i) <span class="vs-amp">&amp;</span> @endif<a href="{{ $l['url'] }}" rel="noopener nofollow" target="_blank">{{ $l['label'] }}</a>@endforeach
      @else
        {{ $label }}
      @endif
    </span>
  </div>
</div>
