@extends('layouts.admin')
@section('title', 'Security')
@section('portal-heading', 'Security')
@section('content')
<x-admin.page-header eyebrow="Admin desk / Security" title="Security status" description="Review production controls and configure bot protection without exposing secrets." />

<div class="admin-dashboard-grid">
    <section class="admin-form-card">
        <div class="admin-section-heading"><div><p class="eyebrow">Control status</p><h2>Application safeguards</h2></div></div>
        <div class="admin-status-list">
            @foreach($checks as $check)
                <div class="admin-status-row"><span class="admin-status-dot {{ $check['ok'] ? 'is-ok' : 'is-warning' }}" aria-hidden="true"></span><span><strong>{{ $check['label'] }}</strong><small>{{ $check['value'] }}</small></span><span class="status-badge {{ $check['ok'] ? 'status-published' : 'status-draft' }}">{{ $check['ok'] ? 'Ready' : 'Review' }}</span></div>
            @endforeach
        </div>
        <p class="field-help">Dependency audits, HTTPS termination, database permissions, backup policy and web-server headers must still be verified in the deployment environment.</p>
    </section>

    <section class="admin-form-card">
        <div class="admin-section-heading"><div><p class="eyebrow">Abuse prevention</p><h2>Google reCAPTCHA v3</h2></div><span class="status-badge {{ $recaptcha->isEnabled() ? 'status-published' : 'status-draft' }}">{{ $recaptcha->isEnabled() ? 'Enabled' : 'Disabled' }}</span></div>
        <p class="field-help">The site key is public by design. The secret is never rendered and is stored encrypted when managed here. Environment values remain the preferred production source.</p>
        <form class="admin-form" method="post" action="{{ route('admin.settings.security.update') }}" data-unsaved-warning>
            @csrf @method('put')
            <label class="admin-check"><input type="checkbox" name="recaptcha_enabled" value="1" @checked($recaptcha->isEnabled())> <span>Enable server-side reCAPTCHA verification</span></label>
            <label>Public site key<input name="recaptcha_site_key" value="{{ old('recaptcha_site_key', $recaptcha->siteKey()) }}" autocomplete="off"></label>
            <label>Secret key <span>(leave blank to keep the current value)</span><input type="password" name="recaptcha_secret_key" value="" autocomplete="new-password" placeholder="{{ $recaptcha->hasSecret() ? 'Configured — masked' : 'Not configured' }}"></label>
            <div class="form-row"><label>Minimum score<input type="number" name="recaptcha_min_score" value="{{ old('recaptcha_min_score', $recaptcha->minScore()) }}" min="0" max="1" step="0.05"></label><label>Expected hostname <span>(optional)</span><input name="recaptcha_hostname" value="{{ old('recaptcha_hostname', $recaptcha->hostname()) }}" placeholder="example.com"></label></div>
            <fieldset class="form-section"><legend>Protected forms</legend><div class="admin-check-grid">@foreach($recaptcha->formSettings() as $form => $enabled)<label class="admin-check"><input type="checkbox" name="recaptcha_forms[{{ $form }}]" value="1" @checked($enabled)> <span>{{ ucfirst($form) }}</span></label>@endforeach</div></fieldset>
            <div class="form-actions"><button class="button button-primary" type="submit">Save security settings <span aria-hidden="true">↗</span></button></div>
        </form>
    </section>
</div>
@endsection
