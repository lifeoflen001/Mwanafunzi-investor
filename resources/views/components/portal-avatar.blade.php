@props(['user', 'size' => 'normal', 'decorative' => false])
<span {{ $attributes->merge(['class' => 'student-avatar student-avatar-'.$size]) }}>
    @if($user?->avatar_url)<img src="{{ $user->avatar_url }}" alt="{{ $decorative ? '' : $user->name }}">@else{{ $user?->initials() ?: 'S' }}@endif
</span>
