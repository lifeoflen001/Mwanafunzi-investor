@extends('layouts.auth')
@section('auth_mode', 'client')
@section('auth_brand_kicker', 'Client portal · Verification')
@section('auth_brand_title', 'Keep the account secure.')
@section('auth_brand_foot', 'Student of Money. Probability. Systems. Discipline.')
@section('title', 'Verify your email')
@section('content')
<div class="auth-form-wrap auth-status-wrap"><div class="auth-form-heading"><p class="auth-form-kicker">Customer portal</p><h2>Check your email.</h2><p>Verify your email address before starting a purchase or enrollment. In local XAMPP mode, verification mail is written to the Laravel log.</p></div><div class="auth-status-actions"><form method="post" action="{{ route('verification.send') }}">@csrf<button class="button auth-primary" type="submit">Resend verification email <span aria-hidden="true">↗</span></button></form>@if($continueUrl && $continueUrl !== route('account.dashboard'))<a class="button auth-secondary" href="{{ $continueUrl }}">Continue after verification <span aria-hidden="true">↗</span></a>@endif</div></div>
@endsection
