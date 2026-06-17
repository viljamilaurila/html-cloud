@extends('pages.content')
@section('title', 'Uploaded from this device — html.cloud')
@section('robots', 'noindex, nofollow')

@section('page')
<section class="uploads-page">
  <h1 class="uploads-title">Uploaded from this device</h1>
  <p class="uploads-note">
    These are files you uploaded in <strong>this browser</strong>. This list — and your ability
    to manage or delete them — lives only on this device, never on our servers. Clear your browser
    data or switch devices and it’s gone, and we can’t recover it. Keep the links somewhere safe if
    they matter.
  </p>

  <div id="uploads-list" class="uploads-list"></div>
  <p id="uploads-empty" class="uploads-empty hidden">Nothing uploaded from this device yet.</p>
</section>
@endsection

@push('scripts')
@vite('resources/js/uploads-page.js')
@endpush
