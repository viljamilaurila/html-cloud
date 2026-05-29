{{-- Cross-links between use-case pages. Pass ['current' => 'use.report'] to omit the current page. --}}
@php
$uses = [
  ['route' => 'use.claude',       'label' => 'Share a Claude artifact'],
  ['route' => 'use.presentation', 'label' => 'Share an AI presentation'],
  ['route' => 'use.report',       'label' => 'Send a client a private report'],
  ['route' => 'use.internal',     'label' => 'Share an internal document'],
];
@endphp
<nav class="compare-links">
  <span class="compare-links-label">More ways to use html.cloud</span>
  @foreach ($uses as $u)
    @if (($current ?? null) !== $u['route'])
      <a href="{{ route($u['route']) }}">{{ $u['label'] }}</a>
    @endif
  @endforeach
</nav>
