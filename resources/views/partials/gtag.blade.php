{{--
  Google Ads tag (gtag.js).

  SAFETY: this must never render on a page whose URL carries a key. gtag reads
  document.location.href and transmits it to Google; on /v/{id}#{viewKey} and
  /e/{id}#{editKey} the fragment IS the decryption key, so loading it there would
  hand Google — and any future version of a script we do not control — the one
  secret the whole product promises we cannot see. See /security.

  It is included by allow-list (home + the marketing content layout), so a new
  key-bearing page inherits nothing. The request guard below is the backstop for
  the day someone moves the include into layout.blade.php.
--}}
@if (($gtagId = config('services.google_ads.id')) && ! request()->is('v/*', 'e/*'))
  <!-- Google tag (gtag.js) -->
  <script async src="https://www.googletagmanager.com/gtag/js?id={{ $gtagId }}"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', @json($gtagId));
  </script>
@endif
