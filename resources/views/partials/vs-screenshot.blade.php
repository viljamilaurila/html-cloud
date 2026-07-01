{{-- Framed, captioned competitor screenshot for comparison pages.
     Usage:
       @include('partials.vs-screenshot', [
         'src'     => 'screenshots/netlify-drop.webp',
         'alt'     => 'Screenshot of …',
         'caption' => 'Netlify Drop's homepage — …',
         'width'   => 1200,
         'height'  => 637,
       ])
     Renders nothing if the file is missing, so a page stays clean if the asset isn't there. --}}
@if (is_file(public_path($src)))
<figure class="vs-shot">
  <img class="vs-shot-img"
       src="{{ asset($src) }}"
       alt="{{ $alt }}"
       width="{{ $width }}"
       height="{{ $height }}"
       loading="lazy"
       decoding="async">
  <figcaption class="vs-shot-cap">{{ $caption }}</figcaption>
</figure>
@endif
