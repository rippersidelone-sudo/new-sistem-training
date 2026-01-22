{{-- resources/views/components/filter-bar.blade.php --}}
@props([
    'action' => '',
    'searchPlaceholder' => 'Cari...',
    'filters' => [],
    'hideSearch' => false,
])

<form method="GET" :action="action" id="filterForm" class="bg-white border rounded-2xl p-5"
    x-data="{ 
        submitWithScroll() {
            // Simpan posisi scroll saat ini
            sessionStorage.setItem('scrollPosition', window.scrollY);
            this.$el.submit();
        }
    }"
    @submit.prevent="submitWithScroll()">
    @php
        $hasActiveFilters = request()->hasAny(array_merge(['search'], collect($filters)->pluck('name')->toArray()));
        $searchColumn = $hideSearch ? 0 : 1;
        $totalColumns = count($filters) + $searchColumn + ($hasActiveFilters ? 1 : 0);
    @endphp
    
    <div class="grid grid-cols-1 lg:grid-cols-{{ $totalColumns }} gap-4">
        
        {{-- Search Input --}}
        @unless($hideSearch)
        <div class="flex items-center bg-[#F1F1F1] rounded-lg px-3 h-[42px]">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="text-[#737373]">
                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
                <path d="M21 21l-6 -6" />
            </svg>
            <input type="text" 
                   name="search" 
                   value="{{ request('search') }}"
                   class="w-full border-0 focus:ring-0 text-sm bg-[#F1F1F1] placeholder-[#737373]"
                   placeholder="{{ $searchPlaceholder }}" />
        </div>
        @endunless

        {{-- Dynamic Dropdown Filters --}}
        @foreach($filters as $filter)
        <div x-data="{ 
            open: false, 
            value: '{{ request($filter['name'], '') }}', 
            label: '{{ request($filter['name']) ? collect($filter['options'])->firstWhere('value', request($filter['name']))['label'] ?? $filter['placeholder'] : $filter['placeholder'] }}' 
        }" class="relative w-full">
            
            {{-- Dropdown Button --}}
            <button type="button" 
                    @click="open = !open"
                    :class="open ? 'border-[#10AF13] ring-1 ring-[#10AF13]' : 'border-gray-300'"
                    class="w-full h-[42px] px-3 py-2 rounded-lg border cursor-pointer flex justify-between items-center text-sm bg-white transition">
                <span x-text="label" class="truncate"></span>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                    stroke="#374151" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="icon icon-tabler icons-tabler-outline icon-tabler-chevron-down flex-shrink-0 ml-2">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M6 9l6 6l6 -6" />
                </svg>
            </button>

            {{-- Dropdown Content --}}
            <div x-show="open" 
                 @click.outside="open = false" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95" 
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-150" 
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="absolute z-20 mt-2 w-full bg-white border rounded-lg shadow-md overflow-hidden max-h-60 overflow-y-auto">

                @foreach($filter['options'] as $option)
                <div @click="
                        value = '{{ $option['value'] }}'; 
                        label = '{{ $option['label'] }}'; 
                        open = false; 
                        $refs.{{ $filter['name'] }}Input.value = value; 
                        sessionStorage.setItem('scrollPosition', window.scrollY);
                        document.getElementById('filterForm').submit();
                    "
                    class="px-3 py-2 text-sm cursor-pointer flex justify-between items-center hover:bg-gray-100">
                    <span>{{ $option['label'] }}</span>
                    <svg x-show="value === '{{ $option['value'] }}'" 
                        xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" 
                        stroke="#10AF13" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M5 12l5 5l10 -10" />
                    </svg>
                </div>
                @endforeach
            </div>

            <input type="hidden" name="{{ $filter['name'] }}" x-ref="{{ $filter['name'] }}Input" :value="value">
        </div>
        @endforeach

        {{-- Reset Button --}}
        @if($hasActiveFilters)
        <div class="flex items-center">
            <a href="{{ $action }}" 
               @click="sessionStorage.setItem('scrollPosition', window.scrollY)"
               class="w-full h-[42px] flex items-center justify-center gap-2 border border-gray-300 text-gray-700 bg-white rounded-lg px-4 text-sm font-medium hover:bg-gray-50 transition whitespace-nowrap">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" 
                    fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4" />
                    <path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4" />
                </svg>
                Reset
            </a>
        </div>
        @endif
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Restore scroll position setelah halaman load
    const savedScrollPosition = sessionStorage.getItem('scrollPosition');
    if (savedScrollPosition) {
        window.scrollTo(0, parseInt(savedScrollPosition));
        sessionStorage.removeItem('scrollPosition');
    }
    
    // Auto-submit search on Enter key
    const searchInput = document.querySelector('#filterForm input[name="search"]');
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                sessionStorage.setItem('scrollPosition', window.scrollY);
                document.getElementById('filterForm').submit();
            }
        });
    }
});
</script>

<style>
/* Custom scrollbar untuk dropdown */
.max-h-60::-webkit-scrollbar {
    width: 6px;
}

.max-h-60::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.max-h-60::-webkit-scrollbar-thumb {
    background: #10AF13;
    border-radius: 10px;
}

.max-h-60::-webkit-scrollbar-thumb:hover {
    background: #0e8e0f;
}
</style>