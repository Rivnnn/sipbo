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
. 'bg-sipbo-panel-light dark:bg-light-panel-light '
. 'border border-sipbo-border dark:border-light-border '
. 'text-sipbo-text dark:text-light-text '
. 'hover:border-sipbo-gold/50 dark:hover:border-sipbo-gold/50 '
. ($sizes[$size] ?? $sizes['md']);
@endphp

@if($href)
<a href="{{ $href }}" {{ $attributes->merge(['class' => $cls]) }}>{{ $slot }}</a>
@else
<button type="{{ $type }}" {{ $attributes->merge(['class' => $cls]) }}>{{ $slot }}</button>
@endif