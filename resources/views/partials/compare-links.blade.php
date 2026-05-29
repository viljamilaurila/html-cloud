{{-- Cross-links between comparison pages. Pass ['current' => 'vs.codepen'] to omit the current page. --}}
@php
$comparisons = [
  ['route' => 'vs.netlify', 'label' => 'vs Netlify Drop'],
  ['route' => 'vs.codepen', 'label' => 'vs CodePen'],
  ['route' => 'vs.drive',   'label' => 'vs Google Drive & Dropbox'],
  ['route' => 'vs.email',   'label' => 'vs emailing an .html file'],
];
@endphp
<nav class="compare-links">
  <span class="compare-links-label">Compare html.cloud</span>
  @foreach ($comparisons as $c)
    @if (($current ?? null) !== $c['route'])
      <a href="{{ route($c['route']) }}">{{ $c['label'] }}</a>
    @endif
  @endforeach
</nav>
