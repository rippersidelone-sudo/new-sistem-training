<!-- resources/views/components/notification.blade.php -->
@props(['type' => 'success'])

@php
    $styles = [
        'success' => 'bg-[#10AF13] text-white',
        'error' => 'bg-red-600 text-white',
        'warning' => 'bg-yellow-500 text-white',
        'info' => 'bg-blue-600 text-white',
    ];
    
    $icons = [
        'success' => '<path d="M5 12l5 5l10 -10" />',
        'error' => '<path d="M10 10l4 4m0 -4l-4 4" />',
        'warning' => '<path d="M12 9v4M12 17h.01" />',
        'info' => '<path d="M12 9v4M12 17h.01" />',
    ];
@endphp

<div x-data="{ show: true }" 
     x-show="show" 
     x-init="setTimeout(() => show = false, 4000)"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 translate-y-4"
     x-transition:enter-end="opacity-100 translate-y-0"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 translate-y-0"
     x-transition:leave-end="opacity-0 translate-y-4"
     class="fixed bottom-6 right-6 z-50 max-w-md">
    <div class="flex items-center gap-3 {{ $styles[$type] }} px-5 py-4 rounded-xl shadow-2xl border border-white/20">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
            class="flex-shrink-0">
            <circle cx="12" cy="12" r="10" />
            {!! $icons[$type] !!}
        </svg>
        <span class="font-medium text-sm">{{ $slot }}</span>
        <button @click="show = false" class="ml-2 hover:opacity-75 transition">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" 
                stroke="currentColor" stroke-width="2">
                <path d="M18 6l-12 12M6 6l12 12" />
            </svg>
        </button>
    </div>
</div>