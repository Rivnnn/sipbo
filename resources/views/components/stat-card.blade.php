@props(['label', 'value', 'tag' => null, 'highlight' => false])

<div class="stat-card-hover
            bg-sipbo-panel dark:bg-light-panel
            border border-sipbo-border dark:border-light-border
            border-l-[3px] {{ $highlight ? 'border-l-sipbo-gold' : 'border-l-transparent pointer-fine:hover:border-l-sipbo-gold' }}
            dark:shadow-sm
            rounded-2xl p-4
            cursor-default">
    <p class="text-xs text-sipbo-text-muted dark:text-light-text-muted mb-1 leading-tight">
        {{ $label }}
    </p>
    <p class="text-xl lg:text-2xl font-bold leading-tight
              {{ $highlight ? 'text-sipbo-gold' : 'text-sipbo-text dark:text-light-text' }}">
        {{ $value }}
    </p>
    @if($tag)
    <span class="inline-block mt-2 text-[10px] px-2.5 py-0.5 rounded-full font-medium
        {{ $highlight
            ? 'bg-sipbo-gold text-sipbo-bg'
            : 'bg-sipbo-panel-light dark:bg-light-panel-light text-sipbo-text-muted dark:text-light-text-muted' }}">
        {{ $tag }}
    </span>
    @endif
</div>