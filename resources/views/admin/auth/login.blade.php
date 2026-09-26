@extends('layouts.admin-auth')
@section('title', 'Admin sign in')
@section('content')
<div class="admin-auth-form-wrap">
    <div class="admin-auth-title"><h2>Sign in to the desk.</h2><p>Use your authorised Mwanafunzi Investor administrator account.</p></div>
    <form class="admin-form admin-auth-form" method="post" action="{{ route('admin.login.store') }}">
        @csrf
        <label for="admin-email">Email address<input id="admin-email" type="email" name="email" required autofocus value="{{ old('email') }}" autocomplete="username"></label>
        <label for="admin-password">Password<input id="admin-password" type="password" name="password" required autocomplete="current-password"></label>
        <label class="admin-check"><input type="checkbox" name="remember" value="1"> <span>Keep me signed in</span></label>
        <button class="button button-primary button-wide" type="submit">Sign in <span aria-hidden="true">↗</span></button>
    </form>
    <a class="admin-auth-back" href="{{ route('home') }}">← Return to public site</a>
</div>
@endsection
