@php
    $hasSuccess = session()->has('success');
    $hasInfo = session()->has('info');
    $hasWarning = session()->has('warning');
    $hasError = $errors->any();
@endphp

@if($hasSuccess || $hasInfo || $hasWarning || $hasError)
    <div class="admin-feedback-stack" aria-live="polite">
        @if($hasSuccess)
            <div class="admin-toast admin-toast-success" role="status" data-admin-toast>
                <span class="admin-toast-icon" aria-hidden="true">✓</span>
                <span>{{ session('success') }}</span>
                <button type="button" class="admin-toast-close" aria-label="Dismiss notification" data-admin-toast-close>×</button>
            </div>
        @endif
        @if($hasInfo)
            <div class="admin-toast admin-toast-info" role="status" data-admin-toast>
                <span class="admin-toast-icon" aria-hidden="true">i</span>
                <span>{{ session('info') }}</span>
                <button type="button" class="admin-toast-close" aria-label="Dismiss notification" data-admin-toast-close>×</button>
            </div>
        @endif
        @if($hasWarning)
            <div class="admin-toast admin-toast-warning" role="status" data-admin-toast>
                <span class="admin-toast-icon" aria-hidden="true">!</span>
                <span>{{ session('warning') }}</span>
                <button type="button" class="admin-toast-close" aria-label="Dismiss notification" data-admin-toast-close>×</button>
            </div>
        @endif
        @if($hasError)
            <div class="admin-toast admin-toast-danger" role="alert" data-admin-toast>
                <span class="admin-toast-icon" aria-hidden="true">!</span>
                <span>{{ $errors->first() }}</span>
                <button type="button" class="admin-toast-close" aria-label="Dismiss notification" data-admin-toast-close>×</button>
            </div>
        @endif
    </div>
@endif
