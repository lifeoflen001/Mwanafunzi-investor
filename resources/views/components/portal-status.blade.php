@props(['status', 'label' => null])
@php($value = is_object($status) && property_exists($status, 'value') ? $status->value : (string) $status)
<span {{ $attributes->merge(['class' => 'student-status student-status-'.str($value)->slug()]) }}>{{ $label ?: str($value)->replace('_', ' ')->title() }}</span>
