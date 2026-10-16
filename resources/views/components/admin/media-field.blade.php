@props(['name', 'label' => 'Image', 'value' => null, 'media' => collect(), 'help' => 'Choose an existing upload or add one from the media library.', 'oldName' => null])
@php
    $fieldValue = old($oldName ?: $name, $value);
    $media = collect($media);
    $selectedIsListed = $fieldValue && $media->contains('path', $fieldValue);
    $selectedMedia = $fieldValue ? $media->firstWhere('path', $fieldValue) : null;
    $selectedUrl = $fieldValue ? (str_starts_with($fieldValue, 'http://') || str_starts_with($fieldValue, 'https://') || str_starts_with($fieldValue, '/') ? $fieldValue : asset('storage/'.ltrim($fieldValue, '/'))) : null;
    $selectedMime = $selectedMedia?->mime_type;
@endphp
<div class="media-picker" data-media-picker data-media-search-url="{{ route('admin.media.search') }}">
    <label>{{ $label }}<input type="search" class="media-picker-search" placeholder="Search uploaded media" aria-label="Search media"><select name="{{ $name }}" data-media-select><option value="">No media selected</option>@if($fieldValue && ! $selectedIsListed)<option value="{{ $fieldValue }}" data-url="{{ $selectedUrl }}" data-mime="{{ $selectedMime }}" selected>{{ basename($fieldValue) }} · current selection</option>@endif @foreach($media as $item)<option value="{{ $item->path }}" data-url="{{ $item->url }}" data-mime="{{ $item->mime_type }}" @selected($fieldValue === $item->path)>{{ $item->filename }}{{ $item->width ? ' · '.$item->width.'×'.$item->height : '' }}</option>@endforeach</select></label>
    <div class="media-picker-loading" data-media-loading hidden aria-hidden="true"><x-skeleton variant="text" width="42%" height="10px" /></div>
    <span class="sr-only" data-media-status aria-live="polite"></span>
    <small class="field-help">{{ $help }} <a href="{{ route('admin.media') }}" target="_blank" rel="noopener">Upload media ↗</a></small>
    <div class="media-picker-preview" data-media-preview @if(! $fieldValue) hidden @endif>@if($fieldValue && str_starts_with((string) $selectedMime, 'video/'))<video src="{{ $selectedUrl }}" controls preload="metadata" aria-label="Selected media preview"></video>@elseif($fieldValue)<img src="{{ $selectedUrl }}" alt="Selected media preview">@endif</div>
</div>
