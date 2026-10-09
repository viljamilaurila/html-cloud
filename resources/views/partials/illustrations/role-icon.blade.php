{{-- Hand-drawn signature object per role, for the situations switcher tabs.
     Usage: @include('partials.illustrations.role-icon', ['role' => 'analyst']) --}}
<svg class="hd-illu hd-role-icon" viewBox="0 0 64 64" fill="none"
     stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
  @switch($role)
    @case('consultant')
      {{-- briefcase --}}
      <path d="M10 24 C24 23 40 23 54 24 C55 34 55 46 54 52 C40 53 24 53 10 52 C9 46 9 34 10 24 Z" style="fill:var(--paper)"/>
      <path d="M24 24 C24 15 40 15 40 24"/>
      <path d="M10 36 C24 37 40 37 54 36"/>
      <path d="M29 34 L35 34 L35 40 L29 40 Z" style="stroke:var(--accent);fill:var(--accent)"/>
      @break
    @case('founder')
      {{-- oversized mug, steaming --}}
      <path d="M12 24 C12 21 42 21 42 24 L40 52 C40 56 14 56 14 52 Z" style="fill:var(--paper)"/>
      <path d="M42 30 C52 30 52 44 41 44"/>
      <path d="M22 16 C19 12 25 9 22 5 M32 16 C29 12 35 9 32 5" style="stroke:var(--accent)"/>
      @break
    @case('designer')
      {{-- pencil, mid-stroke --}}
      <path d="M14 50 L44 12 L52 18 L22 56 Z" style="fill:var(--paper)"/>
      <path d="M14 50 L11 60 L22 56" style="stroke:var(--accent);fill:var(--accent)"/>
      <path d="M40 17 L48 23"/>
      <path d="M30 58 C38 52 48 56 56 50" style="stroke:var(--accent)"/>
      @break
    @case('analyst')
      {{-- round glasses --}}
      <circle cx="20" cy="34" r="10" style="fill:var(--paper)"/>
      <circle cx="44" cy="34" r="10" style="fill:var(--paper)"/>
      <path d="M30 33 C31 30 33 30 34 33 M10 32 L4 26 M54 32 L60 26"/>
      <path d="M16 38 L18 32 M40 38 L42 32" style="stroke:var(--accent)"/>
      @break
    @case('lead')
      {{-- headset --}}
      <path d="M14 36 C12 14 52 14 50 36"/>
      <path d="M9 32 L17 32 L17 46 L9 46 Z M47 32 L55 32 L55 46 L47 46 Z" style="fill:var(--accent);stroke:var(--accent)"/>
      <path d="M51 47 C51 56 44 58 36 57"/>
      <circle cx="34" cy="57" r="2.4" fill="currentColor"/>
      @break
  @endswitch
</svg>
