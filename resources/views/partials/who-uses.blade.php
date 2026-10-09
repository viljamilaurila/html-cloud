{{-- "Who sends private pages?" — real situations, one open at a time.
     Without JS every situation shows, stacked; who-uses.js turns them into tabs. --}}
@php
$roles = [
    [
        'key' => 'consultant',
        'name' => 'Consultant',
        'tag' => 'Proposals & client reports',
        'story' => 'A pricing proposal for one client — rates and margins included.',
        'ours' => 'Only the client can open it, and it can expire when the deal closes.',
    ],
    [
        'key' => 'founder',
        'name' => 'Founder',
        'tag' => 'Investor updates',
        'story' => 'The monthly investor update, with burn and runway in it.',
        'ours' => 'One private link for every investor. Fix a typo and it updates in place.',
    ],
    [
        'key' => 'designer',
        'name' => 'Designer',
        'tag' => 'Prototypes before launch',
        'story' => 'A landing page the client wants to click through before launch.',
        'ours' => 'Opens like the real site — for nobody but the client.',
    ],
    [
        'key' => 'analyst',
        'name' => 'Analyst',
        'tag' => 'Dashboards for outsiders',
        'story' => 'A dashboard of company numbers for a partner outside your workspace.',
        'ours' => 'No account needed to open it. Delete it when the project ends.',
    ],
    [
        'key' => 'lead',
        'name' => 'Team lead',
        'tag' => 'Incident reports',
        'story' => 'The 3 a.m. incident report a customer is waiting for.',
        'ours' => 'A clean, readable report that expires in 7 days.',
    ],
];
@endphp
<section class="who" id="who-uses" aria-labelledby="who-title">
  <p class="who-eyebrow">Real situations</p>
  <h2 class="who-title" id="who-title">AI makes the page. Sending it shouldn’t make it public.</h2>

  <div class="who-tabs" role="tablist" aria-label="Who sends private pages">
    @foreach ($roles as $i => $role)
      <button type="button" class="who-tab" role="tab" id="who-tab-{{ $role['key'] }}"
              aria-controls="who-panel-{{ $role['key'] }}" aria-selected="{{ $i === 0 ? 'true' : 'false' }}"
              tabindex="{{ $i === 0 ? '0' : '-1' }}">
        <span class="who-tab-art">@include('partials.illustrations.role-icon', ['role' => $role['key']])</span>
        <span class="who-tab-name">{{ $role['name'] }}</span>
        <span class="who-tab-tag">{{ $role['tag'] }}</span>
      </button>
    @endforeach
  </div>

  <div class="who-panels">
    @foreach ($roles as $i => $role)
      <article class="who-panel{{ $i === 0 ? ' is-active' : '' }}" role="tabpanel" id="who-panel-{{ $role['key'] }}"
               aria-labelledby="who-tab-{{ $role['key'] }}">
        <div class="who-panel-art">@include('partials.illustrations.role-scene', ['role' => $role['key']])</div>
        <div class="who-panel-body">
          <p class="who-story">{{ $role['story'] }}</p>
          <p class="who-ours"><span aria-hidden="true">✓</span> {{ $role['ours'] }}</p>
        </div>
      </article>
    @endforeach
  </div>
</section>
