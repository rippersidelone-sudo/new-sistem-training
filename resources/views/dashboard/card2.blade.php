<div class="bg-white border rounded-2xl p-6 flex flex-col h-33">
    <!-- TOP: TITLE + ICON -->
    <div class="flex justify-between items-start">
        <h1 class="text-black font-2xl font-bold">
            {{ $title }}
        </h1>

        {{-- <div class="{{ $color ?? '' }}">
            {!! $icon !!}
        </div> --}}
    </div>

    <!-- BOTTOM: VALUE -->
    <div class="mt-8">
        <h2 class="text-lg text-gray-500">
            {{ $text }}
        </h2>
        <p class="text-lg font-semibold text-[#AE00FF]">
            {{ $value }}
        </p>
    </div>
    <div class="mt-2">
        <h2 class="text-lg text-gray-500">
            {{ $text1 }}
        </h2>
        <p class="text-lg font-semibold text-[#FF4D00]">
            {{ $value1 }}
        </p>
    </div>
    <div class="mt-2">
        <h2 class="text-lg text-gray-500">
            {{ $text2 }}
        </h2>
        <p class="text-lg font-semibold text-[#10AF13]">
            {{ $value2 }}
        </p>
    </div>
</div>
