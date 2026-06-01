<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'html.cloud — Private HTML file sharing')</title>
  <meta name="description" content="@yield('description', 'Share an HTML file with a private link. Encrypted in your browser before upload — only people with your link can read it. Not even us. No sign-up.')">
  <meta name="robots" content="@yield('robots', 'index, follow')">

  <!-- Open Graph -->
  <meta property="og:type" content="website">
  <meta property="og:site_name" content="html.cloud">
  <meta property="og:title" content="@yield('og_title', 'html.cloud — Private HTML file sharing')">
  <meta property="og:description" content="@yield('og_description', 'Share an HTML file with a private link. Encrypted in your browser — not even we can read your files.')">
  <meta property="og:url" content="@yield('canonical', config('app.url'))">
  <meta property="og:image" content="{{ config('app.url') }}/og-image.png">
  <meta property="og:image:width" content="1200">
  <meta property="og:image:height" content="630">

  <!-- Twitter / X -->
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="@yield('og_title', 'html.cloud — Private HTML file sharing')">
  <meta name="twitter:description" content="@yield('og_description', 'Share an HTML file with a private link. Encrypted in your browser — not even we can read your files.')">
  <meta name="twitter:image" content="{{ config('app.url') }}/og-image.png">

  <link rel="canonical" href="@yield('canonical', config('app.url'))">

  <link rel="icon" type="image/svg+xml" href="/favicon.svg">
  <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32.png">
  <link rel="apple-touch-icon" href="/apple-touch-icon.png">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Newsreader:ital,opsz,wght@0,6..72,300..600;1,6..72,300..600&family=IBM+Plex+Sans:wght@400;500;600&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  @stack('head')
</head>
<body>
  @yield('content')
  @stack('scripts')
</body>
</html>
