{{-- resources/views/components/tooltip-text.blade.php --}}
@props([
    'text' => '',
    'class' => '',
    'tooltipClass' => '',
    'position' => 'top', // top, bottom, left, right
])

@php
    $positions = [
        'top' => 'bottom-full mb-2 left-0',
        'bottom' => 'top-full mt-2 left-0',
        'left' => 'right-full mr-2 top-0',
        'right' => 'left-full ml-2 top-0',
    ];
    
    $arrows = [
        'top' => 'top-full left-4 -mt-1 border-4 border-transparent border-t-gray-900',
        'bottom' => 'bottom-full left-4 -mb-1 border-4 border-transparent border-b-gray-900',
        'left' => 'left-full top-2 -ml-1 border-4 border-transparent border-l-gray-900',
        'right' => 'right-full top-2 -mr-1 border-4 border-transparent border-r-gray-900',
    ];
    
    $tooltipPosition = $positions[$position] ?? $positions['top'];
    $arrowPosition = $arrows[$position] ?? $arrows['top'];
@endphp

<div class="relative group inline-block w-full">
    <div {{ $attributes->merge(['class' => 'truncate cursor-default ' . $class]) }}>
        {{ $text }}
    </div>
    
    {{-- Custom Tooltip --}}
    <div class="absolute {{ $tooltipPosition }} px-3 py-2 bg-gray-900 text-white text-sm rounded-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 whitespace-normal max-w-xs shadow-lg z-50 pointer-events-none {{ $tooltipClass }}">
        {{ $text }}
        <div class="absolute {{ $arrowPosition }}"></div>
    </div>
</div>