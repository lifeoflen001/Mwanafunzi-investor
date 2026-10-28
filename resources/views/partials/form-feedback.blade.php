@php
    $feedback = collect([
        ['key' => 'success', 'title' => 'Saved successfully', 'message' => session('success'), 'role' => 'status'],
        ['key' => 'info', 'title' => 'Information', 'message' => session('info'), 'role' => 'status'],
        ['key' => 'warning', 'title' => 'Please note', 'message' => session('warning'), 'role' => 'status'],
        ['key' => 'error', 'title' => 'Unable to complete that request', 'message' => session('error'), 'role' => 'alert'],
    ])->filter(fn ($item) => filled($item['message']))->values();
    $validationErrors = $errors ?? new \Illuminate\Support\ViewErrorBag();
    $showValidationDetails = $show_validation_details ?? true;
    $validationFieldKeys = $validationErrors->any()
        ? $validationErrors->getBag('default')->keys()
        : [];
@endphp

@if($feedback->isNotEmpty())
    <div class="feedback-stack admin-feedback-stack" aria-live="polite">
        @foreach($feedback as $item)
            <div class="feedback feedback-{{ $item['key'] }}" role="{{ $item['role'] }}" data-feedback>
                <span class="feedback-icon" aria-hidden="true">{{ $item['key'] === 'success' ? '✓' : ($item['key'] === 'warning' ? '!' : ($item['key'] === 'error' ? '×' : 'i')) }}</span>
                <span class="feedback-copy"><strong>{{ $item['title'] }}</strong><span>{{ $item['message'] }}</span></span>
                <button class="feedback-close" type="button" aria-label="Dismiss notification" data-feedback-dismiss>×</button>
            </div>
        @endforeach
    </div>
@endif

@if($validationErrors->any())
    @if($showValidationDetails)
        <div class="feedback feedback-error feedback-validation-summary" id="form-first-error" role="alert" aria-live="assertive" data-validation-summary data-validation-fields="{{ e(json_encode($validationFieldKeys)) }}">
            <span class="feedback-icon" aria-hidden="true">!</span>
            <span class="feedback-copy">
                <ul>@foreach($validationErrors->all() as $message)<li>{{ $message }}</li>@endforeach</ul>
            </span>
        </div>
    @else
        <span hidden data-validation-summary data-validation-fields="{{ e(json_encode($validationFieldKeys)) }}"></span>
    @endif
@endif
