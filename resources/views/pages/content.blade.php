{{-- Base layout for static content / marketing pages.
     Child pages set @section('title'), SEO @sections, optional @push('head'),
     and put their body in @section('page'). --}}
@extends('layout')

{{-- Analytics lives here rather than in layout.blade.php: the viewer and editor
     also extend that layout, and their URLs carry decryption keys. --}}
@push('head')
@include('partials.gtag')
@endpush

@section('content')
<div class="shell @yield('shellClass', 'swiss-page')">
  @include('partials.topbar')

  <main class="content-main">
    <article class="content-col">
      @yield('page')
    </article>
  </main>

  @include('partials.footer')
</div>
@endsection
