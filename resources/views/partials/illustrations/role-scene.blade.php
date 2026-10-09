{{-- Hand-drawn "who sends what to whom" scenes for the homepage situations
     switcher, in the same loose sketch style as the presenter scene: sender on
     the left, the private link (dashed, locked) in the middle, the people it
     reaches on the right. Ink via currentColor, link and details via --accent.
     Usage: @include('partials.illustrations.role-scene', ['role' => 'founder']) --}}
@php
  // The private link: a dashed cubic curve with the padlock sitting exactly on
  // its midpoint, so the lock always lands on the line.
  $link = function (array $p): string {
      [$x0, $y0, $x1, $y1, $x2, $y2, $x3, $y3] = $p;
      $mx = 0.125 * $x0 + 0.375 * $x1 + 0.375 * $x2 + 0.125 * $x3;
      $my = 0.125 * $y0 + 0.375 * $y1 + 0.375 * $y2 + 0.125 * $y3;
      $lx = $mx - 8;
      $ly = $my - 5;
      return "<path d=\"M$x0 $y0 C$x1 $y1 $x2 $y2 $x3 $y3\" style=\"stroke:var(--accent);stroke-dasharray:5 7\"/>"
          ."<path d=\"M".($lx + 3)." $ly C".($lx + 3)." ".($ly - 7)." ".($lx + 13)." ".($ly - 7)." ".($lx + 13)." $ly\" style=\"stroke:var(--accent)\"/>"
          ."<rect x=\"$lx\" y=\"$ly\" width=\"16\" height=\"13\" rx=\"3\" style=\"fill:var(--bg);stroke:var(--accent)\"/>";
  };
@endphp
<svg class="hd-illu hd-role-scene" viewBox="0 0 360 200" fill="none"
     stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
  @switch($role)
    @case('consultant')
      {{-- Consultant mid-stride, chin up, slicked quiff, briefcase swinging. --}}
      <path d="M30 66 L42 66 M26 80 L38 80 M32 94 L42 94" style="stroke:var(--ink3)"/>
      <circle cx="68" cy="46" r="12" style="fill:var(--paper)"/>
      <path d="M56 42 C57 30 74 27 81 37 C76 34 66 34 58 40"/>
      <path d="M72 31 C77 24 86 27 83 35"/>
      <path d="M57 66 C63 61 73 61 79 66 C83 92 83 116 78 132 C71 135 63 135 57 132 C52 116 52 92 57 66 Z" style="fill:var(--paper)"/>
      <path d="M68 66 L65 72 L68 98 L71 72 Z" style="stroke:var(--accent);fill:var(--accent)"/>
      <path d="M58 74 C48 86 44 98 46 110"/>
      <path d="M78 74 C88 86 93 98 95 107"/>
      <path d="M86 108 C96 107 108 107 118 108 C119 115 119 124 118 130 C108 131 96 131 86 130 C85 124 85 115 86 108 Z" style="fill:var(--paper)"/>
      <path d="M96 108 C96 102 108 102 108 108"/>
      <path d="M63 132 L52 180 M73 132 L86 180 M45 181 L54 181 M86 181 L95 181"/>

      {!! $link([122, 106, 150, 70, 176, 62, 204, 64]) !!}

      {{-- The client's buying team, backs to us, around the meeting-room TV. --}}
      <path d="M210 24 C250 22 306 22 344 24 C346 46 346 82 344 100 C306 102 250 102 210 100 C208 82 208 46 210 24 Z" style="fill:var(--paper)"/>
      <path d="M224 40 C246 39 266 39 284 40" style="stroke:var(--accent)"/>
      <path d="M224 54 C250 53 290 53 320 54 M224 66 C246 65 272 65 296 66"/>
      <path d="M288 76 C302 75 318 75 330 76 C331 81 331 86 330 90 C318 91 302 91 288 90 C287 86 287 81 288 76 Z" style="stroke:var(--accent)"/>
      <path d="M277 101 L277 112"/>
      <circle cx="236" cy="132" r="11" style="fill:var(--paper)"/>
      <circle cx="277" cy="128" r="12" style="fill:var(--paper)"/>
      <circle cx="318" cy="132" r="11" style="fill:var(--paper)"/>
      <path d="M220 160 C221 148 251 148 252 160 M260 158 C261 144 293 144 294 158 M302 160 C303 148 333 148 334 160"/>
      <path d="M327 144 C333 128 335 116 331 106"/>
      <path d="M206 162 C250 158 306 158 350 162"/>
      @break

    @case('founder')
      {{-- Founder: cap on backwards, hoodie strings, laptop balanced on one hand mid-send. --}}
      <circle cx="66" cy="48" r="12" style="fill:var(--paper)"/>
      <path d="M55 43 C55 31 77 31 78 43 C70 41 62 41 55 43 Z" style="fill:var(--accent);stroke:var(--accent)"/>
      <path d="M56 41 C50 41 45 43 42 46" style="stroke:var(--accent);stroke-width:3.4"/>
      <path d="M52 66 C60 61 76 61 84 66 C89 92 89 118 83 136 C75 139 61 139 53 136 C47 118 47 92 52 66 Z" style="fill:var(--paper)"/>
      <path d="M63 68 L62 84 M73 68 L74 84"/>
      <path d="M53 76 C46 90 44 104 48 116"/>
      <path d="M83 76 C90 86 94 94 96 100"/>
      <path d="M92 76 C102 75 114 75 122 76 L121 98 L92 98 Z" style="fill:var(--paper)"/>
      <path d="M97 92 L103 86 L108 89 L116 80" style="stroke:var(--accent)"/>
      <path d="M86 100 C98 99 114 99 126 100"/>
      <path d="M60 136 L56 180 M76 136 L80 180 M48 181 L58 181 M80 181 L90 181"/>

      {!! $link([128, 90, 152, 68, 178, 64, 204, 68]) !!}

      {{-- The investors, on a video call, one tile showing the update. --}}
      <path d="M208 30 C246 28 306 28 352 30 C354 56 354 104 352 126 C306 128 246 128 208 126 C206 104 206 56 208 30 Z" style="fill:var(--paper)"/>
      <path d="M216 40 C216 38 218 38 220 38 L276 38 C278 38 280 38 280 40 L280 76 C280 78 278 78 276 78 L220 78 C218 78 216 78 216 76 Z" style="stroke:var(--ink3)"/>
      <circle cx="248" cy="55" r="7" style="fill:var(--bg)"/>
      <path d="M234 78 C235 66 261 66 262 78"/>
      <path d="M241 53 C242 45 254 45 256 52"/>
      <path d="M282 40 C282 38 284 38 286 38 L342 38 C344 38 346 38 346 40 L346 76 C346 78 344 78 342 78 L286 78 C284 78 282 78 282 76 Z" style="stroke:var(--ink3)"/>
      <circle cx="314" cy="55" r="7" style="fill:var(--bg)"/>
      <path d="M300 78 C301 66 327 66 328 78"/>
      <circle cx="311" cy="55" r="2.4" style="stroke-width:1.8"/><circle cx="317" cy="55" r="2.4" style="stroke-width:1.8"/>
      <path d="M216 82 C216 80 218 80 220 80 L276 80 C278 80 280 80 280 82 L280 118 C280 120 278 120 276 120 L220 120 C218 120 216 120 216 118 Z" style="stroke:var(--ink3)"/>
      <circle cx="248" cy="97" r="7" style="fill:var(--bg)"/>
      <path d="M234 120 C235 108 261 108 262 120"/>
      <path d="M240 98 C239 85 257 85 256 98" style="stroke:var(--accent)"/>
      <path d="M282 82 C282 80 284 80 286 80 L342 80 C344 80 346 80 346 82 L346 118 C346 120 344 120 342 120 L286 120 C284 120 282 120 282 118 Z" style="stroke:var(--accent)"/>
      <path d="M290 89 C302 88 314 88 322 89" style="stroke:var(--accent)"/>
      <path d="M290 112 L302 104 L312 108 L336 94" style="stroke:var(--accent)"/>
      <path d="M280 127 L280 142 M262 144 C272 142 288 142 298 144"/>
      @break

    @case('designer')
      {{-- Designer: beret at an angle, hand on hip, sketchbook under the arm, pencil behind the ear. --}}
      <circle cx="66" cy="50" r="12" style="fill:var(--paper)"/>
      <path d="M51 44 C53 31 78 28 85 40 C77 44 60 46 51 44 Z" style="fill:var(--accent);stroke:var(--accent)"/>
      <path d="M68 32 L69 26"/>
      <path d="M78 52 L92 40"/>
      <path d="M90 42 L95 37" style="stroke:var(--accent)"/>
      <path d="M55 68 C61 63 71 63 77 68 C81 94 81 118 76 134 C69 137 61 137 55 134 C50 118 50 94 55 68 Z" style="fill:var(--paper)"/>
      <path d="M58 66 C62 70 70 70 74 66"/>
      <path d="M56 76 C44 86 44 98 56 104"/>
      <path d="M84 78 C102 76 112 78 114 80 C116 96 116 112 114 120 C104 122 92 120 82 118 Z" style="fill:var(--paper)"/>
      <path d="M90 92 C96 86 104 88 106 96 C100 102 92 100 90 92 Z" style="stroke:var(--accent)"/>
      <path d="M76 76 C82 86 84 96 84 104"/>
      <path d="M60 134 L50 180 M71 134 L84 180 M43 181 L52 181 M84 181 L93 181"/>

      {!! $link([124, 96, 150, 70, 178, 66, 204, 70]) !!}

      {{-- The client, pointing at the not-yet-public landing page. --}}
      <path d="M212 54 C238 52 270 52 296 54 C298 74 298 104 296 122 C270 124 238 124 212 122 C210 104 210 74 212 54 Z" style="fill:var(--paper)"/>
      <path d="M222 66 C244 65 266 65 286 66 L286 84 C266 85 244 85 222 84 Z" style="stroke:var(--accent)"/>
      <path d="M222 96 C238 95 256 95 270 96 M222 106 C234 105 246 105 256 106"/>
      <path d="M262 102 C270 101 280 101 286 102 L286 112 C280 113 270 113 262 112 Z" style="stroke:var(--accent)"/>
      <path d="M200 132 C236 128 276 128 306 132 L296 124 M212 124 L200 132"/>
      <path d="M272 108 L272 120 L276 116 L280 123 L282 122 L278 115 L284 115 Z" style="fill:var(--accent);stroke:var(--accent);stroke-width:1.6"/>
      <circle cx="326" cy="60" r="12" style="fill:var(--paper)"/>
      <path d="M314 54 C316 44 334 42 338 52"/>
      <path d="M314 78 C320 73 332 73 338 78 C342 104 342 128 337 144 C330 147 322 147 316 144 C311 128 311 104 314 78 Z" style="fill:var(--paper)"/>
      <path d="M316 88 C304 94 294 100 286 104"/>
      <path d="M337 90 C344 102 346 112 344 122"/>
      <path d="M320 144 L316 182 M332 144 L336 182"/>
      @break

    @case('analyst')
      {{-- Analyst leaning in at a big monitor: round glasses, hand on chin, unconvinced. --}}
      <path d="M12 52 C34 50 70 50 90 52 C92 68 92 96 90 110 C70 112 34 112 12 110 C10 96 10 68 12 52 Z" style="fill:var(--paper)"/>
      <path d="M22 98 L22 86 M32 98 L32 76 M42 98 L42 82 M52 98 L52 70" style="stroke:var(--accent)"/>
      <path d="M62 94 L70 84 L76 88 L84 74" style="stroke:var(--accent)"/>
      <path d="M51 111 L51 126"/>
      <circle cx="114" cy="72" r="12" style="fill:var(--paper)"/>
      <circle cx="108" cy="72" r="4"/>
      <circle cx="119" cy="72" r="4"/>
      <path d="M112 72 L115 72 M102 63 L110 61"/>
      <path d="M102 90 C108 86 120 86 126 92 C129 104 129 116 127 126 L101 126 C99 114 99 102 102 90 Z" style="fill:var(--paper)"/>
      <path d="M102 98 C96 92 100 86 106 84"/>
      <path d="M6 128 C46 125 100 125 140 128 M14 130 L14 182 M132 130 L132 182"/>
      <path d="M100 126 L98 182 M116 126 L120 182"/>

      {!! $link([142, 92, 164, 70, 186, 64, 208, 66]) !!}

      {{-- The partner team, two heads over one laptop. --}}
      <circle cx="240" cy="70" r="12" style="fill:var(--paper)"/>
      <path d="M228 66 C230 56 248 54 252 64"/>
      <circle cx="306" cy="68" r="12" style="fill:var(--paper)"/>
      <path d="M296 60 C300 52 314 52 318 62 C316 60 306 58 298 62"/>
      <path d="M226 136 C224 110 230 90 240 88 C252 90 258 110 256 136 M292 136 C290 108 296 88 306 86 C318 88 324 108 322 136"/>
      <path d="M248 100 C258 106 264 112 266 118"/>
      <path d="M254 92 C270 91 288 91 300 92 C301 102 301 114 300 120 C288 121 270 121 254 120 C253 114 253 102 254 92 Z" style="fill:var(--paper)"/>
      <circle cx="277" cy="106" r="8" style="stroke:var(--accent)"/>
      <path d="M277 98 L277 102" style="stroke:var(--accent)"/>
      <path d="M248 128 L306 128 M210 138 C254 134 300 134 344 138"/>
      @break

    @case('lead')
      {{-- Team lead at 03:07: headset on, hunched at an alert, two empty coffee cups. --}}
      <text x="78" y="38" style="stroke:none;fill:var(--accent);font-family:var(--mono);font-size:13px">03:07</text>
      <circle cx="32" cy="76" r="12" style="fill:var(--paper)"/>
      <path d="M20 74 C18 58 46 56 46 72" style="stroke:var(--accent)"/>
      <path d="M17 70 L23 70 L23 82 L17 82 Z" style="fill:var(--accent);stroke:var(--accent)"/>
      <path d="M44 80 C48 86 52 88 56 88"/>
      <path d="M20 92 C28 88 40 88 46 96 C50 110 50 124 46 136 L20 136 C16 122 16 106 20 92 Z" style="fill:var(--paper)"/>
      <path d="M44 100 C56 106 66 112 74 118"/>
      <path d="M66 48 C84 47 112 47 130 48 C131 64 131 90 130 104 C112 105 84 105 66 104 C65 90 65 64 66 48 Z" style="fill:var(--paper)"/>
      <path d="M98 58 L112 84 L84 84 Z" style="stroke:var(--accent)"/>
      <path d="M98 66 L98 74 M98 79 L98 80" style="stroke:var(--accent)"/>
      <path d="M78 94 C92 93 106 93 120 94" style="stroke:var(--ink3)"/>
      <path d="M98 105 L98 118"/>
      <path d="M118 112 C118 108 128 108 128 112 L127 124 L119 124 Z M132 116 C132 113 140 113 140 116 L139 124 L133 124 Z"/>
      <path d="M8 126 C50 123 104 123 150 126 M20 128 L20 182 M140 128 L140 182 M24 136 L22 182 M40 136 L44 182"/>

      {!! $link([150, 92, 172, 68, 192, 62, 214, 66]) !!}

      {{-- The customer's IT person, calm, mid-morning, reading the report. --}}
      <path d="M226 46 C254 44 290 44 316 46 C318 64 318 92 316 108 C290 110 254 110 226 108 C224 92 224 64 226 46 Z" style="fill:var(--paper)"/>
      <path d="M238 58 C256 57 276 57 292 58" style="stroke:var(--accent)"/>
      <path d="M238 72 C260 71 284 71 304 72 M238 84 C256 83 274 83 290 84 M238 96 C258 95 280 95 300 96"/>
      <path d="M271 109 L271 124"/>
      <circle cx="334" cy="84" r="11" style="fill:var(--paper)"/>
      <path d="M324 82 C324 70 344 70 345 80"/>
      <path d="M322 100 C328 96 340 96 346 102 C348 116 348 128 346 136 L322 136 C320 124 320 110 322 100 Z" style="fill:var(--paper)"/>
      <path d="M324 110 C316 116 310 120 304 122"/>
      <path d="M206 128 C252 125 302 125 350 128"/>
      <path d="M216 120 C216 112 230 112 230 120 L229 126 L217 126 Z"/>
      @break
  @endswitch
</svg>
