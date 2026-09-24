<div style="font-family:Arial,sans-serif;max-width:640px;margin:0 auto;color:#27231f">
    <h1>{{ $heading }}</h1>
    <p>{{ $body }}</p>
    @if($actionUrl && $actionLabel)<p><a href="{{ $actionUrl }}">{{ $actionLabel }} ↗</a></p>@endif
    <p style="color:#746d66">Educational content only. This is not personalised financial advice.</p>
    <p>Thanks,<br>{{ config('app.name', 'Mwanafunzi Investor') }}</p>
</div>
