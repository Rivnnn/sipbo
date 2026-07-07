@props([
'id',
'title' => 'Modal',
'size' => 'md',
])

@php
$sizes = [
'sm' => 'max-w-sm',
'md' => 'max-w-lg',
'lg' => 'max-w-2xl',
'xl' => 'max-w-4xl',
];
$maxW = $sizes[$size] ?? $sizes['md'];
@endphp

<div
    x-data="{ open: false }"
    x-on:open-modal-{{ $id }}.window="open = true"
    x-on:close-modal-{{ $id }}.window="open = false"
    x-on:keydown.escape.window="open = false">

    {{-- Backdrop + Modal --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        style="display: none;">

        {{-- Backdrop --}}
        <div
            @click="open = false"
            class="absolute inset-0 bg-black/60 dark:bg-black/40 backdrop-blur-sm"></div>

        {{-- Modal Panel --}}
        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            @click.stop
            data-modal-id="{{ $id }}"
            class="relative w-full {{ $maxW }} z-10
                   bg-sipbo-panel dark:bg-light-panel
                   border border-sipbo-border dark:border-light-border
                   rounded-2xl shadow-2xl overflow-hidden">

            {{-- Header --}}
            <div class="flex items-center justify-between px-5 py-4
                        border-b border-sipbo-border dark:border-light-border">
                <h3 class="font-semibold text-sipbo-text dark:text-light-text text-base">
                    {{ $title }}
                </h3>
                <button @click="open = false"
                    class="w-8 h-8 rounded-lg flex items-center justify-center
                           text-sipbo-text-muted dark:text-light-text-muted
                           hover:bg-sipbo-panel-light dark:hover:bg-light-panel-light
                           hover:text-sipbo-text dark:hover:text-light-text transition">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            {{-- Body --}}
            <div class="px-5 py-4 overflow-y-auto max-h-[75vh]">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>