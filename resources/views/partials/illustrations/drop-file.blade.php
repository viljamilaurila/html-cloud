{{-- Drop-zone hero: the "Your HTML file" document from the flow, dropping in.
     Document outline in currentColor (ink); the </> slash + drop arrow in --accent. --}}
<svg class="hd-illu hd-drop" viewBox="0 0 150 150" fill="none"
     stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"
     role="img" aria-label="An HTML file dropping into the drop zone">
  <g transform="translate(5 -4)">
    {{-- document body + folded corner --}}
    <path d="M44 30 C43 27.6 45 26.4 47.4 26.2 C62 25.3 77 24.8 90 25.6 L100 36
             C101 52 101 78 99.6 100 C99.4 103.6 97.6 105.4 95 105.4
             C77 106.4 60 106.2 46 105.4 C43 105.2 41.4 103.4 41.3 100.6
             C40.4 76 40.6 50 44 30 Z" style="fill:var(--paper)"/>
    <path d="M90 25.6 C91 31 91.2 35.4 91.8 39.4 C95.6 40 99.6 40 100 36"/>
    {{-- </> code mark --}}
    <path d="M63 60 L56 69 L63 78"/>
    <path d="M72 58 L66 80" style="stroke:var(--accent)"/>
    <path d="M77 60 L84 69 L77 78"/>
  </g>
  {{-- drop arrow: the file falls into the zone --}}
  <g style="stroke:var(--accent)">
    <path d="M75 112 L75 138"/>
    <path d="M64 129 L75 140 L86 129"/>
  </g>
</svg>
