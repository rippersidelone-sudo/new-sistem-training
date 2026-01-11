{{-- resources/views/components/filter-bar.blade.php --}}

@props([
    'action' => '',
    'searchPlaceholder' => 'Cari...',
    'filters' => [],
    'activeFiltersCount' => 0,
])

<form method="GET" 
      action="{{ $action }}" 
      class="flex flex-col sm:flex-row sm:items-end sm:gap-3 gap-4" 
      x-data="{
          isSubmitting: false,
          scrollPosition: 0,
          submitFilter() {
              this.scrollPosition = window.scrollY;
              this.isSubmitting = true;

              const form = this.$el;
              const formData = new FormData(form);
              const params = new URLSearchParams(formData).toString();
              const url = form.action + (form.action.includes('?') ? '&' : '?') + params;

              fetch(url, {
                  method: 'GET',
                  headers: {
                      'X-Requested-With': 'XMLHttpRequest',
                      'Accept': 'text/html'
                  }
              })
              .then(response => response.text())
              .then(html => {
                  const parser = new DOMParser();
                  const doc = parser.parseFromString(html, 'text/html');

                  // Ganti hanya bagian daftar user + pagination
                  // Sesuaikan selector dengan struktur HTML halamanmu
                  const newContent = doc.querySelector('.grid.gap-6.mt-8.px-2');
                  if (newContent) {
                      const target = document.querySelector('.grid.gap-6.mt-8.px-2');
                      if (target) target.outerHTML = newContent.outerHTML;
                  }

                  // Kembalikan posisi scroll
                  setTimeout(() => {
                      window.scrollTo(0, this.scrollPosition);
                      this.isSubmitting = false;
                  }, 100);

                  // Update URL di address bar (opsional tapi sangat berguna)
                  history.pushState({}, '', url);
              })
              .catch(() => {
                  // Fallback jika AJAX gagal
                  form.submit();
              });
          }
      }"
      @submit.prevent="submitFilter">

    <!-- Search + Filters + Buttons dalam satu baris di layar besar -->
    <div class="flex flex-1 flex-col sm:flex-row sm:items-end gap-3 w-full">

        <!-- Search Input -->
        <div class="flex-1 min-w-[240px]">
            <div class="flex items-center bg-[#F9FAFB] border border-gray-200 rounded-lg px-4 py-2.5 focus-within:border-[#10AF13] focus-within:ring-2 focus-within:ring-[#10AF13]/20 transition">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="text-gray-400 flex-shrink-0">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
                    <path d="M21 21l-6 -6" />
                </svg>
                <input type="text" 
                    name="search" 
                    value="{{ request('search') }}"
                    @input.debounce.500ms="$el.form.submit()"
                    class="w-full border-0 focus:ring-0 text-sm bg-transparent placeholder-gray-400 ml-2 p-0"
                    placeholder="{{ $searchPlaceholder }}" />
            </div>
        </div>

        <!-- Dynamic Filters (dropdowns) - lebar dibatasi lebih ketat -->
        @foreach($filters as $filter)
        <div class="min-w-[180px] max-w-[220px]" x-data="{ 
            open: false, 
            value: '{{ request($filter['name']) }}', 
            label: '{{ collect($filter['options'])->firstWhere('value', request($filter['name']))['label'] ?? $filter['placeholder'] }}'
        }">
            <button type="button"
                @click="open = !open"
                :class="value && value !== '' ? 'border-[#10AF13] bg-[#10AF13]/5' : 'border-gray-200 bg-[#F9FAFB]'"
                class="w-full px-4 py-2.5 rounded-lg border cursor-pointer flex justify-between items-center text-sm hover:border-[#10AF13] transition group">
                <span x-text="label" class="truncate" :class="value && value !== '' ? 'text-[#10AF13] font-medium' : 'text-gray-600'"></span>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="flex-shrink-0 transition-transform"
                    :class="open ? 'rotate-180' : ''"
                    :style="value && value !== '' ? 'stroke: #10AF13' : 'stroke: #6B7280'">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M6 9l6 6l6 -6" />
                </svg>
            </button>

            <!-- Dropdown content juga dibatasi lebarnya -->
            <div x-show="open" 
                 @click.outside="open = false" 
                 x-transition
                 class="absolute z-30 mt-1 w-full max-w-[220px] bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden">
                <div class="max-h-60 overflow-y-auto">
                    @foreach($filter['options'] as $option)
                    <div @click="
                            value = '{{ $option['value'] }}'; 
                            label = '{{ $option['label'] }}'; 
                            open = false;
                            $nextTick(() => $el.closest('form').submitFilter())
                        "
                        :class="value === '{{ $option['value'] }}' ? 'bg-[#10AF13]/10 text-[#10AF13]' : 'hover:bg-gray-50'"
                        class="px-4 py-2.5 text-sm cursor-pointer flex justify-between items-center transition">
                        <span class="font-medium">{{ $option['label'] }}</span>
                        <svg x-show="value === '{{ $option['value'] }}'" 
                            xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" 
                            stroke="#10AF13" stroke-width="2.5" class="flex-shrink-0">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M5 12l5 5l10 -10" />
                        </svg>
                    </div>
                    @endforeach
                </div>
            </div>

            <input type="hidden" name="{{ $filter['name'] }}" :value="value">
        </div>
        @endforeach
    </div>

    <!-- Action Buttons -->
    <div class="flex items-center gap-3 sm:self-end">
        <button type="submit"
            :disabled="isSubmitting"
            class="flex items-center bg-[#10AF13] text-white rounded-lg px-5 py-2.5 text-sm font-semibold hover:bg-[#0e8e0f] transition shadow-sm hover:shadow-md disabled:opacity-50 disabled:cursor-not-allowed whitespace-nowrap">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" 
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M4 4h6v6h-6z" />
                <path d="M14 4h6v6h-6z" />
                <path d="M4 14h6v6h-6z" />
                <path d="M17 17m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />
            </svg>
            <span x-show="!isSubmitting">Filter</span>
            <span x-show="isSubmitting" x-cloak>Memfilter...</span>
        </button>

        @if($activeFiltersCount > 0)
        <a href="{{ request()->url() }}"
            class="flex items-center border border-gray-300 text-gray-700 bg-white rounded-lg px-4 py-2.5 text-sm font-medium hover:bg-gray-50 transition whitespace-nowrap">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" 
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4" />
                <path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4" />
            </svg>
            Reset
        </a>
        @endif

        @if($activeFiltersCount > 0)
        <div class="hidden sm:flex items-center gap-2 text-sm">
            <span class="text-gray-500">Aktif:</span>
            <span class="inline-flex items-center justify-center w-6 h-6 bg-[#10AF13] text-white text-xs font-bold rounded-full">
                {{ $activeFiltersCount }}
            </span>
        </div>
        @endif
    </div>
</form>