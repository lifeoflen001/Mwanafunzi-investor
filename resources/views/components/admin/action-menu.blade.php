@props(['label' => 'Actions'])
<details class="admin-action-menu">
    <summary aria-label="{{ $label }}">•••</summary>
    <div class="admin-action-menu-list">{{ $slot }}</div>
</details>
