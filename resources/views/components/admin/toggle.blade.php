@props([
    'name',
    'label',
    'checked' => false,
    'help' => null,
])
<label class="admin-toggle">
    <input type="hidden" name="{{ $name }}" value="0">
    <input type="checkbox" name="{{ $name }}" value="1" @checked($checked)>
    <span class="admin-toggle-track" aria-hidden="true"><span></span></span>
    <span class="admin-toggle-copy"><strong>{{ $label }}</strong>@if($help)<small>{{ $help }}</small>@endif</span>
</label>
