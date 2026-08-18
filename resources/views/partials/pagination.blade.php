@php
    /** @var \Illuminate\Pagination\LengthAwarePaginator $rows */
    $cp = $rows->currentPage();
    $lp = $rows->lastPage();
    $win = [];
    for ($i = 1; $i <= $lp; $i++) {
        if ($i == 1 || $i == $lp || abs($i - $cp) <= 1) $win[$i] = $i;
    }
    $keys = array_keys($win);
@endphp

@if ($lp > 0)
    <div class="flex items-center gap-1 text-xs">
        {{-- First / Prev --}}
        <a href="{{ $rows->url(1) }}"
           class="px-2.5 py-1.5 rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50 hover:text-blue-500 transition-colors {{ $rows->onFirstPage() ? 'pointer-events-none opacity-40' : '' }}">
            <i class="fa-solid fa-angles-left text-[10px]"></i>
        </a>
        <a href="{{ $rows->previousPageUrl() }}"
           class="px-2.5 py-1.5 rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50 hover:text-blue-500 transition-colors {{ $rows->onFirstPage() ? 'pointer-events-none opacity-40' : '' }}">
            <i class="fa-solid fa-angle-left text-[10px]"></i>
        </a>

        {{-- Page numbers --}}
        @php $prevK = 0; @endphp
        @foreach ($keys as $k)
            @if ($k - $prevK > 1)
                <span class="px-2.5 py-1.5 text-gray-300 select-none">…</span>
            @endif
            <a href="{{ $rows->url($k) }}"
               class="min-w-[32px] px-2.5 py-1.5 rounded-lg border text-center font-medium transition-colors
                      {{ $k == $cp
                         ? 'bg-blue-500 border-blue-500 text-white shadow-sm'
                         : 'border-gray-200 text-gray-600 hover:bg-gray-50 hover:text-blue-500' }}">
                {{ $k }}
            </a>
            @php $prevK = $k; @endphp
        @endforeach

        {{-- Next / Last --}}
        <a href="{{ $rows->nextPageUrl() }}"
           class="px-2.5 py-1.5 rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50 hover:text-blue-500 transition-colors {{ $rows->hasMorePages() ? '' : 'pointer-events-none opacity-40' }}">
            <i class="fa-solid fa-angle-right text-[10px]"></i>
        </a>
        <a href="{{ $rows->url($lp) }}"
           class="px-2.5 py-1.5 rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50 hover:text-blue-500 transition-colors {{ $rows->hasMorePages() ? '' : 'pointer-events-none opacity-40' }}">
            <i class="fa-solid fa-angles-right text-[10px]"></i>
        </a>
    </div>
@endif