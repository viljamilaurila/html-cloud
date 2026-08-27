{{-- html.cloud logomark: hand-drawn cloud with an upload arrow, matching the
     hd-illu sketch style. Outline renders in currentColor, arrow in --accent,
     body in --paper. Same art as public/favicon.svg (which bakes fixed colors
     plus a dark-mode media query instead of CSS variables). --}}
@php
  $s = $size ?? 16;
@endphp

<svg class="cloud-mark" width="{{ $s }}" height="{{ $s }}" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
  <path d="M11.5 37 C5 36.5 3.5 28.5 9.5 26.5
           C8.5 17.5 18 12.5 23.5 16.5
           C27 8.5 39 9.5 40.5 18
           C46.5 17.5 48.5 26.5 43.5 29.5
           C46.5 32.5 44.5 37 39.5 36.9
           C30 37.9 19 37.8 11.5 37 Z"
        stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" style="fill:var(--paper)"/>
  <path d="M24 33 L24 25" stroke-width="4.4" stroke-linecap="round" style="stroke:var(--accent)"/>
  <path d="M24 18.6 L17.6 27.2 L30.4 27.2 Z" stroke-width="2.4" stroke-linejoin="round" style="fill:var(--accent);stroke:var(--accent)"/>
</svg>
