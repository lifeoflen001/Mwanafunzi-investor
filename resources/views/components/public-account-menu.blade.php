@props(['user'])
<div class="public-account-menu" data-public-account>
    <button class="public-account-toggle" type="button" aria-expanded="false" aria-haspopup="menu" aria-controls="public-account-menu" data-public-account-toggle>
        <span class="public-account-avatar" aria-hidden="true">@if($user->avatar_url)<img src="{{ $user->avatar_url }}" alt="">@else{{ $user->initials() }}@endif</span>
        <span class="public-account-label">{{ $user->firstName() }}</span>
        <span class="public-account-caret" aria-hidden="true">⌄</span>
    </button>
    <div class="public-account-popover" id="public-account-menu" hidden data-public-account-popover>
        <div class="public-account-popover-head"><span class="public-account-avatar" aria-hidden="true">@if($user->avatar_url)<img src="{{ $user->avatar_url }}" alt="">@else{{ $user->initials() }}@endif</span><span><strong>{{ $user->name }}</strong><small>{{ $user->email }}</small></span></div>
        <nav aria-label="Account navigation">
            <a href="{{ route('account.dashboard') }}">My Dashboard <span aria-hidden="true">↗</span></a>
            <a href="{{ route('account.profile') }}">My profile</a>
            <a href="{{ route('account.courses') }}">My learning</a>
            <a href="{{ route('account.orders') }}">Orders &amp; receipts</a>
            <a href="{{ route('account.security') }}">Account security</a>
        </nav>
        <form method="post" action="{{ route('logout') }}">
            @csrf
            <button type="submit">Sign out</button>
        </form>
    </div>
</div>
