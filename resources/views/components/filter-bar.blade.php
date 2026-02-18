{{-- resources/views/components/filter-bar.blade.php --}}
@props([
    'action' => '',
    'searchPlaceholder' => 'Cari...',
    'filters' => [],
    'hideSearch' => false,
])

{{-- ✅ FIXED: Changed :action to action (removed Alpine binding) --}}
<form method="GET" action="{{ $action }}" id="filterForm" class="bg-white border rounded-2xl p-5"
    x-data="filterBarController()"
    x-init="initForm()"
    @submit.prevent="submitForm()">
    
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
                   @keypress.enter="submitForm()"
                   class="w-full border-0 focus:ring-0 text-sm bg-[#F1F1F1] placeholder-[#737373]"
                   placeholder="{{ $searchPlaceholder }}" />
        </div>
        @endunless

        {{-- Dynamic Dropdown Filters --}}
        @foreach($filters as $index => $filter)
        <div x-data="dropdownController('{{ $filter['name'] }}', '{{ request($filter['name'], '') }}', @js($filter['options']), '{{ $filter['placeholder'] ?? 'Pilih...' }}')" 
             x-init="initDropdown()"
             @click.outside="closeDropdown()"
             class="relative w-full">
            
            {{-- Dropdown Button --}}
            <button type="button" 
                    @click.prevent.stop="toggleDropdown()"
                    :class="isOpen ? 'border-[#10AF13] ring-1 ring-[#10AF13]' : 'border-gray-300'"
                    class="w-full h-[42px] px-3 py-2 rounded-lg border cursor-pointer flex justify-between items-center text-sm bg-white transition">
                <span x-text="currentLabel" class="truncate"></span>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                    stroke="#374151" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    :class="isOpen ? 'rotate-180' : ''"
                    class="flex-shrink-0 ml-2 transition-transform">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M6 9l6 6l6 -6" />
                </svg>
            </button>

            {{-- Dropdown Content --}}
            <div x-show="isOpen"
                 x-cloak
                 @click.outside="closeDropdown()" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95" 
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-150" 
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="absolute z-20 mt-2 w-full bg-white border rounded-lg shadow-md overflow-hidden max-h-60 overflow-y-auto">

                <template x-for="option in options" :key="option.value">
                    <div @click.stop="selectOption(option.value, option.label)"
                        class="px-3 py-2 text-sm cursor-pointer flex justify-between items-center hover:bg-gray-100"
                        :class="currentValue === option.value ? 'bg-[#10AF13]/5 text-[#10AF13]' : ''">
                        <span x-text="option.label"></span>
                        <svg x-show="currentValue === option.value" 
                            x-cloak
                            xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" 
                            stroke="#10AF13" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M5 12l5 5l10 -10" />
                        </svg>
                    </div>
                </template>
            </div>

            <input type="hidden" 
                   :name="fieldName" 
                   :value="currentValue"
                   x-ref="hiddenInput">
        </div>
        @endforeach

        {{-- Reset Button --}}
        @if($hasActiveFilters)
        <div class="flex items-center">
            <a href="{{ $action }}" 
               @click="saveScrollPosition()"
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

@push('scripts')
<script>
// ✅ CRITICAL FIX: Filter bar controller
function filterBarController() {
    return {
        initForm() {
            // Force close all dropdowns
            this.$nextTick(() => {
                this.forceCloseAllDropdowns();
            });
        },
        
        forceCloseAllDropdowns() {
            const dropdowns = this.$el.querySelectorAll('[x-data*="dropdownController"]');
            dropdowns.forEach(dropdown => {
                if (dropdown.__x && dropdown.__x.$data) {
                    dropdown.__x.$data.isOpen = false;
                }
            });
        },
        
        submitForm() {
            this.saveScrollPosition();
            this.$el.submit();
        },
        
        saveScrollPosition() {
            sessionStorage.setItem('scrollPosition', window.scrollY);
        }
    }
}

// ✅ Dropdown controller untuk setiap filter
function dropdownController(name, initialValue, options, placeholder) {
    placeholder = placeholder || 'Pilih...';
    const selectedOption = initialValue ? options.find(opt => String(opt.value) === String(initialValue)) : null;
    
    return {
        fieldName: name,
        options: options,
        currentValue: initialValue || '',
        currentLabel: selectedOption ? selectedOption.label : placeholder,
        isOpen: false,
        
        initDropdown() {
            // Force closed state
            this.isOpen = false;
            
            this.$nextTick(() => {
                this.isOpen = false;
            });
        },
        
        toggleDropdown() {
            this.isOpen = !this.isOpen;
        },
        
        closeDropdown() {
            this.isOpen = false;
        },
        
        selectOption(value, label) {
            this.currentValue = value;
            this.currentLabel = label;
            this.isOpen = false;
            
            // Save scroll and submit
            sessionStorage.setItem('scrollPosition', window.scrollY);
            
            // Small delay to ensure state is updated
            this.$nextTick(() => {
                setTimeout(() => {
                    document.getElementById('filterForm').submit();
                }, 50);
            });
        }
    }
}

// ✅ CRITICAL: Force close all dropdowns on page load
document.addEventListener('alpine:init', () => {
    setTimeout(() => {
        document.querySelectorAll('[x-data*="dropdownController"]').forEach(el => {
            if (el.__x && el.__x.$data) {
                el.__x.$data.isOpen = false;
            }
        });
    }, 50);
});

// ✅ Restore scroll position
document.addEventListener('DOMContentLoaded', function() {
    const scrollPos = sessionStorage.getItem('scrollPosition');
    if (scrollPos) {
        setTimeout(() => {
            window.scrollTo({
                top: parseInt(scrollPos),
                behavior: 'instant'
            });
            sessionStorage.removeItem('scrollPosition');
        }, 100);
    }
});
</script>
@endpush

<style>
/* Scrollbar styling */
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

/* Ensure dropdowns are hidden by default */
[x-cloak] {
    display: none !important;
}
</style>