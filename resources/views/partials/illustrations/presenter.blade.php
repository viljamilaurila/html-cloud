{{-- Hand-drawn presenter + chart scene (client report / proposal pages).
     Single-weight sketch lines. Ink via currentColor, chart + pointer via --accent. --}}
<svg class="hd-illu hd-presenter" viewBox="0 0 360 222" fill="none"
     stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"
     role="img" aria-label="A person presenting a chart on a screen">
  {{-- floor --}}
  <path d="M34 206 C120 203 250 203 330 206"/>

  {{-- presentation board --}}
  <path d="M196 32 C232 30 296 30 326 32 C328 60 328 110 326 137 C296 139 232 139 196 137
           C194 110 194 60 196 32 Z" style="fill:var(--paper)"/>
  {{-- stand --}}
  <path d="M261 138 L261 198"/>
  <path d="M246 200 C254 197 268 197 276 200"/>

  {{-- donut chart + bars (accent) --}}
  <circle cx="252" cy="76" r="24" style="stroke:var(--accent)"/>
  <circle cx="252" cy="76" r="11" style="stroke:var(--accent)"/>
  <path d="M252 52 L252 65" style="stroke:var(--accent)"/>
  <path d="M214 124 L214 108" style="stroke:var(--accent)"/>
  <path d="M226 124 L226 100" style="stroke:var(--accent)"/>
  <path d="M238 124 L238 112" style="stroke:var(--accent)"/>

  {{-- presenter --}}
  <circle cx="92" cy="64" r="13" style="fill:var(--paper)"/>
  <path d="M78 62 C78 46 106 46 106 62"/>
  {{-- body --}}
  <path d="M79 86 C86 80 99 80 106 86 C110 114 110 144 104 162 C96 165 88 165 81 162
           C75 144 75 114 79 86 Z" style="fill:var(--paper)"/>
  {{-- near arm down --}}
  <path d="M81 94 C73 106 71 120 75 134"/>
  {{-- far arm raised, pointing --}}
  <path d="M104 90 C122 86 138 82 150 78"/>
  {{-- pointer stick (accent) --}}
  <path d="M150 78 L196 66" style="stroke:var(--accent)"/>
  {{-- legs --}}
  <path d="M87 162 L85 199"/>
  <path d="M98 162 L100 199"/>
</svg>
