@props([
'size' => 'md',
'type' => 'button',
'href' => null,
])

@php
$sizes = [
'sm' => 'px-3 py-1.5 text-xs',
'md' => 'px-4 py-2 text-sm',
'lg' => 'px-5 py-2.5 text-base',
];
$cls = 'inline-flex items-center gap-1.5 font-semibold rounded-lg transition active:scale-[0.97] '
. 'bg-red-900/30 dark:bg-red-50 '
. 'border border-red-700/40 dark:border-red-200 '
. 'text-red-400 dark:text-red-700 '
. 'hover:bg-red-900/50 dark:hover:bg-red-100 '
. ($sizes[$size] ?? $sizes['md']);
@endphp

@if($href)
<a href="{{ $href }}" {{ $attributes->merge(['class' => $cls]) }}>{{ $slot }}</a>
@else
<button type="{{ $type }}" {{ $attributes->merge(['class' => $cls]) }}>{{ $slot }}</button>
@endif