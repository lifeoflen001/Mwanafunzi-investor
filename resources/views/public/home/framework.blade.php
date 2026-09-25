@php
    $steps = $section->payload['steps'] ?? [];
@endphp
<section class="framework-section" id="framework"><div class="container"><div class="framework-header reveal"><p class="eyebrow eyebrow-light"><span class="eyebrow-line"></span> {{ $section->payload['eyebrow'] ?? '' }}</p><h2>{{ $section->heading }}</h2><p>{{ $section->body }}</p></div><div class="framework-steps">@foreach($steps as $step)<div class="framework-step reveal"><span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><strong>{{ $step['title'] ?? '' }}</strong><p>{{ $step['body'] ?? '' }}</p></div>@endforeach</div></div></section>
