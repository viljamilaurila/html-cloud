{{-- "Which one will Claude use?" — a working mock of the extension's install-time
     switch. Off (default): Claude routes each request on its own — casual and
     public pages stay artifacts, sensitive or outside-the-org pages go to
     html.cloud. On: every share is a private link. State is pure CSS
     (:has(:checked)), so no script is needed. --}}
@php
$rows = [
  ['ask' => 'Mock up a pricing page for me',            'default' => 'artifact'],
  ['ask' => 'Send the proposal to the client',          'default' => 'cloud'],
  ['ask' => 'Put the launch page up for everyone',      'default' => 'artifact'],
  ['ask' => 'Turn the Q3 numbers into a one-pager',     'default' => 'cloud'],
];
@endphp
<div class="route-demo" aria-label="How Claude chooses between an artifact and html.cloud">
  <label class="route-switch">
    <input type="checkbox" class="route-check">
    <span class="route-toggle" aria-hidden="true"></span>
    <span class="route-switch-text">
      <span class="route-switch-label">Always share through html.cloud</span>
      <span class="route-hint route-hint-off">Off — Claude decides per request. Try flipping it.</span>
      <span class="route-hint route-hint-on">On — every share becomes a private link.</span>
    </span>
  </label>

  <div class="route-rows">
    @foreach ($rows as $row)
      <div class="route-row route-default-{{ $row['default'] }}">
        <span class="route-ask">“{{ $row['ask'] }}”</span>
        <svg class="route-arrow" width="44" height="12" viewBox="0 0 44 12" fill="none" aria-hidden="true">
          <path class="route-arrow-line" d="M1 6h36" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
          <path d="M33 1.5 38 6l-5 4.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <span class="route-dest">
          <span class="route-badge route-badge-artifact">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="1.5" y="2.5" width="13" height="11" rx="2"/><path d="M1.5 6h13M4.5 4.3h.01M6.5 4.3h.01"/></svg>
            <span><b>Claude artifact</b><small>in the chat · publish if it's public</small></span>
          </span>
          <span class="route-badge route-badge-cloud">
            @include('partials.cloud-mark', ['size' => 16])
            <span><b>html.cloud</b><small>encrypted · private link</small></span>
          </span>
        </span>
      </div>
    @endforeach
  </div>
</div>
