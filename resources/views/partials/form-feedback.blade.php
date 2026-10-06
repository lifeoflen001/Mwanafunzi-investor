@php
    $feedback = collect([
        ['key' => 'success', 'title' => 'Saved successfully', 'message' => session('success'), 'role' => 'status'],
        ['key' => 'info', 'title' => 'Information', 'message' => session('info'), 'role' => 'status'],
        ['key' => 'warning', 'title' => 'Please note', 'message' => session('warning'), 'role' => 'status'],
        ['key' => 'error', 'title' => 'Unable to complete that request', 'message' => session('error'), 'role' => 'alert'],
    ])->filter(fn ($item) => filled($item['message']))->values();
    $validationErrors = $errors ?? new \Illuminate\Support\ViewErrorBag();
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
    <div class="feedback feedback-error feedback-validation-summary" id="form-first-error" role="alert" aria-live="assertive">
        <span class="feedback-icon" aria-hidden="true">!</span>
        <span class="feedback-copy">
            <strong>Please correct {{ $validationErrors->count() === 1 ? 'the highlighted field' : $validationErrors->count().' highlighted fields' }}.</strong>
            <span><a href="#form-first-error">Review the form errors below.</a></span>
            <ul>@foreach($validationErrors->all() as $message)<li>{{ $message }}</li>@endforeach</ul>
        </span>
    </div>
@endif
