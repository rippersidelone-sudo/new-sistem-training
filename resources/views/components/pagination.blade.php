{{-- resources/views/components/pagination.blade.php --}}
@props(['paginator'])

@if ($paginator->hasPages())
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 py-4">
        {{-- Info Text --}}
        <div class="text-sm text-gray-600">
            Menampilkan 
            <span class="font-semibold text-gray-900">{{ $paginator->firstItem() }}</span>
            -
            <span class="font-semibold text-gray-900">{{ $paginator->lastItem() }}</span>
            dari
            <span class="font-semibold text-gray-900">{{ $paginator->total() }}</span>
            hasil
        </div>

        {{-- Pagination Buttons --}}
        <div class="flex items-center gap-2">
            {{-- Previous Button --}}
            @if ($paginator->onFirstPage())
                <span class="px-3 py-2 text-sm font-medium text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M15 18l-6-6 6-6"/>
                    </svg>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M15 18l-6-6 6-6"/>
                    </svg>
                </a>
            @endif

            {{-- Page Numbers (Desktop) --}}
            <div class="hidden sm:flex items-center gap-1">
                @php
                    $start = max($paginator->currentPage() - 2, 1);
                    $end = min($start + 4, $paginator->lastPage());
                    $start = max($end - 4, 1);
                @endphp

                {{-- First Page --}}
                @if($start > 1)
                    <a href="{{ $paginator->url(1) }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                        1
                    </a>
                    @if($start > 2)
                        <span class="px-2 py-2 text-sm font-medium text-gray-400">...</span>
                    @endif
                @endif

                {{-- Page Numbers --}}
                @for ($i = $start; $i <= $end; $i++)
                    @if ($i == $paginator->currentPage())
                        <span class="px-4 py-2 text-sm font-semibold text-white bg-[#10AF13] rounded-lg shadow-md">
                            {{ $i }}
                        </span>
                    @else
                        <a href="{{ $paginator->url($i) }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                            {{ $i }}
                        </a>
                    @endif
                @endfor

                {{-- Last Page --}}
                @if($end < $paginator->lastPage())
                    @if($end < $paginator->lastPage() - 1)
                        <span class="px-2 py-2 text-sm font-medium text-gray-400">...</span>
                    @endif
                    <a href="{{ $paginator->url($paginator->lastPage()) }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                        {{ $paginator->lastPage() }}
                    </a>
                @endif
            </div>

            {{-- Current Page (Mobile) --}}
            <div class="sm:hidden">
                <span class="px-4 py-2 text-sm font-semibold text-white bg-[#10AF13] rounded-lg">
                    {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}
                </span>
            </div>

            {{-- Next Button --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 18l6-6-6-6"/>
                    </svg>
                </a>
            @else
                <span class="px-3 py-2 text-sm font-medium text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 18l6-6-6-6"/>
                    </svg>
                </span>
            @endif
        </div>
    </div>
@endif