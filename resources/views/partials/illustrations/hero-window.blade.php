{{-- Home hero vignette: a sketched browser window showing a page, padlocked, with two
     sparkles — "your HTML file, live at a private link". Single-weight sketch lines.
     Ink via currentColor, the lock keyhole + sparkles + one text rule via --accent. --}}
<svg class="hd-illu hd-hero-window" viewBox="0 0 300 220" fill="none"
     stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"
     role="img" aria-label="A web page in a browser window, locked, with sparkles">
  {{-- window frame (slightly wobbly edges) --}}
  <path d="M24 40 C23 35 26 32 31 31.6 C100 29 180 28.4 262 30.4 C267 30.6 270 33.6 270 38.4
           C271.4 84 271 130 269.6 178 C269.4 183 266.6 185.6 262 185.8
           C182 188.4 100 188 32 186.2 C27.2 186 24.4 183.2 24.2 178.6
           C22.6 132 22.8 86 24 40 Z" style="fill:var(--paper)"/>
  {{-- title bar rule --}}
  <path d="M25 66 C105 64.4 185 64.4 269 66"/>
  {{-- traffic-light dots --}}
  <circle cx="42" cy="48.5" r="3.4"/>
  <circle cx="56" cy="48.5" r="3.4"/>
  <circle cx="70" cy="48.5" r="3.4"/>
  {{-- page content: one accent heading rule + body rules --}}
  <path d="M48 92 C72 90.6 96 90.6 122 92" style="stroke:var(--accent);stroke-width:3.4"/>
  <path d="M48 112 C80 110.8 112 110.8 146 112"/>
  <path d="M48 130 C82 128.8 116 128.8 152 130"/>
  <path d="M48 148 C76 146.8 104 146.8 134 148"/>
  {{-- media block on the right --}}
  <path d="M178 88 C182 85.6 184.6 85.4 188 85.6 C204 85 220 85 236 85.6
           C239.6 85.8 242 88 242 91.6 C242.8 110 242.6 130 242 150
           C241.8 153.6 239.6 155.8 236 156 C220 156.6 204 156.6 188 156
           C184.4 155.8 182.2 153.6 182 150 C181 130 181 110 178 88 Z"
        style="fill:var(--bg)"/>
  {{-- padlock, hanging off the bottom-right of the window --}}
  <path d="M218 168 C228 166.8 246 166.8 256 168.2 C258.2 176 258 190 256 197.4
           C246 199.4 228 199.4 218.4 197.4 C216.4 190 216.4 176 218 168 Z"
        style="fill:var(--paper)"/>
  <path d="M224.5 167.5 C223.5 159 224.4 154 237 153.8 C249.6 154 250.2 159 249.6 167.6"/>
  <circle cx="237" cy="180.5" r="3.4" style="stroke:var(--accent)"/>
  <path d="M237 183.6 L234.8 190.4 L239.2 190.4 Z" style="stroke:var(--accent);fill:var(--accent)"/>
  {{-- sparkles (accent) --}}
  <g style="stroke:var(--accent)">
    <path d="M272 26 C274.6 33 278 36.4 285 39 C278 41.6 274.6 45 272 52
             C269.4 45 266 41.6 259 39 C266 36.4 269.4 33 272 26 Z" style="fill:var(--bg)"/>
    <path d="M252 8 C253.4 12 255.4 14 259.4 15.4 C255.4 16.8 253.4 18.8 252 22.8
             C250.6 18.8 248.6 16.8 244.6 15.4 C248.6 14 250.6 12 252 8 Z" style="fill:var(--bg)"/>
  </g>
</svg>
