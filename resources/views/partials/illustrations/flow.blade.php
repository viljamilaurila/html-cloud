{{-- Hand-drawn three-step flow: HTML file -> encrypted -> hosted & shareable.
     Single-weight sketch lines. Ink via currentColor, key actions via --accent. --}}
<svg class="hd-illu hd-flow" viewBox="0 0 460 150" fill="none"
     stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"
     role="img" aria-label="Drop an HTML file, your browser encrypts it, and it's hosted as a private shareable link">

  {{-- ── Step 1: HTML file ── --}}
  <g>
    <path d="M44 30 C43 27.6 45 26.4 47.4 26.2 C62 25.3 77 24.8 90 25.6 L100 36
             C101 52 101 78 99.6 100 C99.4 103.6 97.6 105.4 95 105.4
             C77 106.4 60 106.2 46 105.4 C43 105.2 41.4 103.4 41.3 100.6
             C40.4 76 40.6 50 44 30 Z" style="fill:var(--paper)"/>
    <path d="M90 25.6 C91 31 91.2 35.4 91.8 39.4 C95.6 40 99.6 40 100 36"/>
    {{-- </> code mark --}}
    <path d="M63 60 L56 69 L63 78"/>
    <path d="M72 58 L66 80" style="stroke:var(--accent)"/>
    <path d="M77 60 L84 69 L77 78"/>
    <text x="70" y="131" class="hd-cap">Your HTML file</text>
  </g>

  {{-- arrow 1 --}}
  <g>
    <path d="M118 66 C126 64 134 64 142 65.5"/>
    <path d="M136 60 L143 65.6 L136 71"/>
  </g>

  {{-- ── Step 2: encrypted (padlock) ── --}}
  <g>
    <path d="M206 64 C218 62.8 240 62.8 252 64 C254.2 72 254 90 252 97.4
             C240 99.4 220 99.4 206.4 97.4 C204.4 90 204.4 72 206 64 Z"
          style="fill:var(--paper)"/>
    <path d="M213 63.5 C212 54.5 212.8 49 230 48.8 C247 49 247.6 54.5 247 63.6"/>
    <circle cx="230" cy="78" r="3.6" style="stroke:var(--accent)"/>
    <path d="M230 81.4 L227.4 89 L232.6 89 Z" style="stroke:var(--accent);fill:var(--accent)"/>
    <text x="230" y="131" class="hd-cap">Encrypted in your browser</text>
  </g>

  {{-- arrow 2 --}}
  <g>
    <path d="M278 66 C286 64 294 64 302 65.5"/>
    <path d="M296 60 L303 65.6 L296 71"/>
  </g>

  {{-- ── Step 3: hosted & shareable (cloud) ── --}}
  <g>
    <path d="M360 92 C351 92 348.5 80.5 357.5 77 C356 67 368 60.5 377 66
             C381.5 54.5 401 54.5 405.5 66 C416 62.5 425 71.5 420 80.5
             C429 81.5 428 93 419 92 C406 93 374 93 360 92 Z"
          style="fill:var(--paper)"/>
    {{-- upload/share arrow into the cloud --}}
    <path d="M390 88 L390 71" style="stroke:var(--accent)"/>
    <path d="M383 77 L390 70 L397 77" style="stroke:var(--accent)"/>
    <text x="390" y="131" class="hd-cap">Ready to share</text>
  </g>
</svg>
