{{-- Hand-drawn "locked HTML document" hero vignette.
     Single-weight sketch lines. Ink via currentColor, key detail via --accent.
     Usage: @include('partials.illustrations.locked-doc') --}}
<svg class="hd-illu hd-locked-doc" viewBox="0 0 150 150" fill="none"
     stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"
     role="img" aria-label="An HTML document, locked">
  {{-- paper sheet (slightly wavy edges, folded top-right corner) --}}
  <path d="M42 27 C41 24.5 43 23.2 45.5 23 C62 22 79 21.4 96 22.2 L113 38
           C114 60 114 95 112.6 120 C112.4 124 110.4 126 107.5 126
           C86 127.2 64 127 46 126.2 C42.8 126 41 124 40.9 121
           C39.8 90 40 56 42 27 Z"
        style="fill:var(--paper)"/>
  {{-- folded corner flap --}}
  <path d="M96 22.2 C97.2 28 97.4 33 98 37.4 C102.6 38 107.6 38 113 38"/>
  {{-- a couple of text lines on the sheet --}}
  <path d="M53.5 49 C65 48.2 77 48.2 89.5 49.4"/>
  <path d="M53.5 60.5 C63 59.8 73 59.8 83 60.6"/>
  {{-- padlock body (over the lower sheet) --}}
  <path d="M57.5 92 C70 90.8 86 90.8 96.5 92.2 C98.6 100 98.4 112 96.4 119.4
           C84 121.4 68 121.4 57.6 119.4 C55.6 112 55.6 99.8 57.5 92 Z"
        style="fill:var(--paper)"/>
  {{-- padlock shackle --}}
  <path d="M64 91.5 C63 83.5 63.8 78.5 77 78.4 C90 78.5 90.4 84.5 90 91.6"/>
  {{-- keyhole (accent) --}}
  <circle cx="77" cy="102.5" r="3.6" style="stroke:var(--accent)"/>
  <path d="M77 105.8 L74.6 113 L79.4 113 Z" style="stroke:var(--accent);fill:var(--accent)"/>
</svg>
