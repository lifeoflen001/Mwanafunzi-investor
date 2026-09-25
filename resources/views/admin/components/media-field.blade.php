@props(['name', 'label' => 'Image', 'value' => null, 'media' => collect(), 'help' => 'Choose an existing upload or add one from the media library.', 'oldName' => null])
@php
    $fieldValue = old($oldName ?: $name, $value);
    $media = collect($media);
    $selectedIsListed = $fieldValue && $media->contains('path', $fieldValue);
    $selectedUrl = $fieldValue
        ? (str_starts_with($fieldValue, 'http://') || str_starts_with($fieldValue, 'https://') || str_starts_with($fieldValue, '/')
            ? $fieldValue
            : asset('storage/'.ltrim($fieldValue, '/')))
        : null;
@endphp
<div class="media-picker" data-media-picker data-media-search-url="{{ route('admin.media.search') }}">
    <label>{{ $label }}
        <input type="search" class="media-picker-search" placeholder="Search uploaded media" aria-label="Search media">
        <select name="{{ $name }}" data-media-select>
            <option value="">No image selected</option>
            @if($fieldValue && ! $selectedIsListed)
                <option value="{{ $fieldValue }}" data-url="{{ $selectedUrl }}" selected>{{ basename($fieldValue) }} · current selection</option>
            @endif
            @foreach($media as $item)
                <option value="{{ $item->path }}" data-url="{{ $item->url }}" @selected($fieldValue === $item->path)>{{ $item->filename }}{{ $item->width ? ' · '.$item->width.'×'.$item->height : '' }}{{ $item->size ? ' · '.number_format($item->size / 1024, 1).' KB' : '' }}</option>
            @endforeach
        </select>
    </label>
    <small class="field-help">{{ $help }} <a href="{{ route('admin.media') }}" target="_blank" rel="noopener">Upload media ↗</a></small>
    <div class="media-picker-preview" data-media-preview @if(! $fieldValue) hidden @endif>@if($fieldValue)<img src="{{ $selectedUrl }}" alt="Selected media preview">@endif</div>
</div>
