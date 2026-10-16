@props(['redirect' => null, 'link' => false])
@php($googleUrl = $link ? route('account.security.google.redirect') : route('auth.google.redirect', array_filter(['redirect' => $redirect])))
<a class="google-auth-button" href="{{ $googleUrl }}">
    <svg class="google-auth-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
        <path fill="#4285F4" d="M21.35 12.23c0-.72-.06-1.42-.18-2.09H12v3.96h5.23a4.47 4.47 0 0 1-1.94 2.93v2.44h3.14c1.84-1.7 2.92-4.2 2.92-7.24Z"/>
        <path fill="#34A853" d="M12 21.5c2.63 0 4.84-.87 6.45-2.36l-3.14-2.44c-.87.58-1.98.93-3.31.93-2.54 0-4.7-1.72-5.47-4.03H3.28v2.52A9.75 9.75 0 0 0 12 21.5Z"/>
        <path fill="#FBBC05" d="M6.53 13.6a5.86 5.86 0 0 1 0-3.2V7.88H3.28a9.5 9.5 0 0 0 0 8.24l3.25-2.52Z"/>
        <path fill="#EA4335" d="M12 6.37c1.43 0 2.71.49 3.72 1.45l2.79-2.79C16.84 3.48 14.63 2.5 12 2.5a9.75 9.75 0 0 0-8.72 5.38l3.25 2.52C7.3 8.09 9.46 6.37 12 6.37Z"/>
    </svg>
    <span>{{ $link ? 'Connect Google' : 'Continue with Google' }}</span>
</a>
