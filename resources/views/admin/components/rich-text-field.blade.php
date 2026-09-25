@props([
    'name',
    'label' => 'Content',
    'value' => null,
    'help' => 'Basic formatting is supported and unsafe HTML is removed before publishing.',
    'idSuffix' => null,
])
@php
    $editorId = 'rich-editor-'.str_replace(['[', ']', '.', ' '], '-', $name).'-'.substr(md5($name.'|'.($idSuffix ?? '')), 0, 6);
    $safeValue = \App\Support\RichText::sanitize((string) ($value ?? ''));
@endphp
<div class="rich-text-field" data-rich-editor-wrapper>
    <label for="{{ $editorId }}"><span>{{ $label }}</span></label>
    <div class="rich-text-toolbar" role="toolbar" aria-label="{{ $label }} formatting">
        <button type="button" data-rich-command="formatBlock" data-rich-value="p" title="Paragraph">P</button>
        <button type="button" data-rich-command="formatBlock" data-rich-value="h2" title="Heading">H2</button>
        <button type="button" data-rich-command="formatBlock" data-rich-value="h3" title="Subheading">H3</button>
        <button type="button" data-rich-command="bold" title="Bold"><strong>B</strong></button>
        <button type="button" data-rich-command="italic" title="Italic"><em>I</em></button>
        <button type="button" data-rich-command="insertUnorderedList" title="Bulleted list">• List</button>
        <button type="button" data-rich-command="insertOrderedList" title="Numbered list">1. List</button>
        <button type="button" data-rich-command="formatBlock" data-rich-value="blockquote" title="Blockquote">Quote</button>
        <button type="button" data-rich-command="createLink" title="Add link">Link</button>
        <button type="button" data-rich-command="removeFormat" title="Clear formatting">Clear</button>
    </div>
    <div id="{{ $editorId }}" class="rich-text-editor" contenteditable="true" role="textbox" aria-multiline="true" data-rich-editor>{!! $safeValue !!}</div>
    <textarea name="{{ $name }}" hidden data-rich-source>{{ $value }}</textarea>
    @if($help)<small class="field-help">{{ $help }}</small>@endif
</div>
