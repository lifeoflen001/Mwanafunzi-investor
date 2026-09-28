@props(['lines' => 3, 'class' => ''])
<div {{ $attributes->merge(['class' => 'admin-skeleton '.$class]) }} aria-hidden="true">
    @for($line = 0; $line < $lines; $line++)<span></span>@endfor
</div>
