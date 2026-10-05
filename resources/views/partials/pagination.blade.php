@php
    $link = 'relative inline-flex items-center px-3 py-2 text-sm font-medium ring-1 ring-inset ring-slate-300 dark:ring-slate-700';
    $enabled = $link . ' text-slate-700 hover:bg-slate-50 dark:text-slate-300 dark:hover:bg-slate-800';
    $disabled = $link . ' cursor-default text-slate-300 dark:text-slate-600';
    $start = max($paginator->currentPage() - 2, 1);
    $end = min($paginator->currentPage() + 2, $paginator->lastPage());
@endphp

<div class="flex flex-col gap-3 border-t border-slate-200 px-4 py-3 sm:flex-row sm:items-center sm:justify-between dark:border-slate-800">
    <p class="text-sm text-slate-600 dark:text-slate-400">
        @if ($paginator->total())
            Showing <span class="font-medium">{{ number_format($paginator->firstItem()) }}</span>
            to <span class="font-medium">{{ number_format($paginator->lastItem()) }}</span>
            of <span class="font-medium">{{ number_format($paginator->total()) }}</span> results
        @else
            No results
        @endif
    </p>

    @if ($paginator->hasPages())
        <nav class="isolate inline-flex -space-x-px rounded-lg shadow-sm" aria-label="Pagination">
            @if ($paginator->onFirstPage())
                <span class="{{ $disabled }} rounded-l-lg" aria-disabled="true"><span class="sr-only">Previous</span>&lsaquo;</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="{{ $enabled }} rounded-l-lg"><span class="sr-only">Previous</span>&lsaquo;</a>
            @endif

            @if ($start > 1)
                <a href="{{ $paginator->url(1) }}" class="{{ $enabled }} hidden sm:inline-flex">1</a>
                @if ($start > 2)
                    <span class="{{ $disabled }} hidden sm:inline-flex">&hellip;</span>
                @endif
            @endif

            @foreach ($paginator->getUrlRange($start, $end) as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span aria-current="page" class="relative z-10 inline-flex items-center bg-indigo-600 px-3 py-2 text-sm font-semibold text-white">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="{{ $enabled }}">{{ $page }}</a>
                @endif
            @endforeach

            @if ($end < $paginator->lastPage())
                @if ($end < $paginator->lastPage() - 1)
                    <span class="{{ $disabled }} hidden sm:inline-flex">&hellip;</span>
                @endif
                <a href="{{ $paginator->url($paginator->lastPage()) }}" class="{{ $enabled }} hidden sm:inline-flex">{{ $paginator->lastPage() }}</a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="{{ $enabled }} rounded-r-lg"><span class="sr-only">Next</span>&rsaquo;</a>
            @else
                <span class="{{ $disabled }} rounded-r-lg" aria-disabled="true"><span class="sr-only">Next</span>&rsaquo;</span>
            @endif
        </nav>
    @endif
</div>
