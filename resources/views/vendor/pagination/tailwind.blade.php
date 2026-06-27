@if ($paginator->hasPages())
<nav class="flex items-center justify-between">
    <div class="flex justify-between flex-1 sm:hidden">
        @if ($paginator->onFirstPage())
        <span class="px-3 py-1.5 text-sm text-sipbo-text-muted bg-sipbo-panel-light border border-sipbo-border rounded-lg cursor-not-allowed">Prev</span>
        @else
        <a href="{{ $paginator->previousPageUrl() }}" class="px-3 py-1.5 text-sm text-sipbo-text bg-sipbo-panel-light border border-sipbo-border rounded-lg hover:bg-sipbo-gold hover:text-sipbo-bg">Prev</a>
        @endif

        @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" class="px-3 py-1.5 text-sm text-sipbo-text bg-sipbo-panel-light border border-sipbo-border rounded-lg hover:bg-sipbo-gold hover:text-sipbo-bg">Next</a>
        @else
        <span class="px-3 py-1.5 text-sm text-sipbo-text-muted bg-sipbo-panel-light border border-sipbo-border rounded-lg cursor-not-allowed">Next</span>
        @endif
    </div>

    <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
        <div>
            <p class="text-sm text-sipbo-text-muted">
                Menampilkan <span class="font-medium text-sipbo-text">{{ $paginator->firstItem() }}</span>
                - <span class="font-medium text-sipbo-text">{{ $paginator->lastItem() }}</span>
                dari <span class="font-medium text-sipbo-text">{{ $paginator->total() }}</span> hasil
            </p>
        </div>
        <div>
            <span class="relative z-0 inline-flex gap-1">
                @foreach ($elements as $element)
                @if (is_string($element))
                <span class="px-3 py-1.5 text-sm text-sipbo-text-muted">{{ $element }}</span>
                @endif

                @if (is_array($element))
                @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                <span class="px-3 py-1.5 text-sm font-semibold bg-sipbo-gold text-sipbo-bg rounded-lg">{{ $page }}</span>
                @else
                <a href="{{ $url }}" class="px-3 py-1.5 text-sm text-sipbo-text bg-sipbo-panel-light border border-sipbo-border rounded-lg hover:bg-sipbo-gold hover:text-sipbo-bg">{{ $page }}</a>
                @endif
                @endforeach
                @endif
                @endforeach
            </span>
        </div>
    </div>
</nav>
@endif