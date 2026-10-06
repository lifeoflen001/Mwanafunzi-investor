@props(['user', 'size' => 'normal'])
<span {{ $attributes->merge(['class' => 'student-avatar student-avatar-'.$size]) }}>
    @if($user?->avatar_url)<img src="{{ $user->avatar_url }}" alt="{{ $user->name }}">@else{{ $user?->initials() ?: 'S' }}@endif
</span>
