@props(['label' => 'Actions'])
<details class="admin-action-menu">
    <summary aria-label="{{ $label }}" aria-haspopup="menu">•••</summary>
    <div class="admin-action-menu-list" role="menu">{{ $slot }}</div>
</details>
