{{-- html.cloud logomark: a cloud, solid on the left and pixelating on the right
     (plaintext → encrypted). Pixels sit on a regular 4-unit grid. Monochrome,
     renders in currentColor. --}}
@php
  $s = $size ?? 24;
  $cid = 'cloudClip-' . uniqid();
  // Grid-aligned pixels: the cloud's lower-right breaks into a staircase.
  $pixels = [[12, 7], [12, 11], [12, 15], [16, 11], [16, 15], [20, 15]];
@endphp
<svg class="brand-icon cloud-mark" viewBox="0 0 24 24" width="{{ $s }}" height="{{ $s }}" fill="currentColor" aria-hidden="true" focusable="false">
  <defs>
    <clipPath id="{{ $cid }}">
      <path d="M19.35 10.04A7.49 7.49 0 0 0 12 4C9.11 4 6.6 5.64 5.35 8.04A5.994 5.994 0 0 0 0 14c0 3.31 2.69 6 6 6h13c2.76 0 5-2.24 5-5 0-2.64-2.05-4.78-4.65-4.96z"/>
    </clipPath>
  </defs>
  <g clip-path="url(#{{ $cid }})">
    {{-- solid (plaintext) left side --}}
    <rect x="-1" y="0" width="12.5" height="24"/>
    {{-- pixel staircase (encrypted) on the right --}}
    @foreach ($pixels as [$x, $y])
      <rect x="{{ $x }}" y="{{ $y }}" width="3.3" height="3.3" rx="0.3"/>
    @endforeach
  </g>
</svg>
