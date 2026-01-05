<div class="bg-white border rounded-2xl p-6 flex flex-col h-33 hover:shadow-md transition">
    <!-- TOP: Title + Status -->
    <div class="flex justify-between items-start">
        <h1 class="text-black font-medium text-xl">
            {{ $title }}
        </h1>
    </div>

    <!-- Sub Title -->
    <div class="flex justify-between">
        <div class="mt-4 text-gray-600 gap-2 items-center">
            @if (!empty($statusPelatihan))
                <h2 class="text-md font-medium">
                    {{ $statusPelatihan }}
                </h2>
            @endif
            <div class="px-3 py-1 w-fit text-xs uppercase font-medium rounded-full {{ $colorStatusPelatihan ?? '' }}">
                @if (!empty($value))
                    {!! $value !!}
                @endif
            </div>
        </div>
        <div class="mt-4 text-gray-600 gap-2 items-center">
            @if (!empty($statusAbsensi))
                <h2 class="text-md font-medium">
                    {{ $statusAbsensi }}
                </h2>
            @endif
            <div class="px-3 py-1 w-fit flex items-center gap-2 text-xs uppercase font-medium rounded-full {{ $colorStatusAbsensi ?? '' }}">
                @if (!empty($icon))
                    <p class="text-lg">
                        {!! $icon !!}
                    </p>
                @endif
                @if (!empty($value1))
                    {!! $value1 !!}
                @endif
            </div>
        </div>
    </div>

    <!-- BOTTOM: VALUE -->
    <div class="mt-6 flex gap-2 text-gray-600 items-center">
        @if (!empty($icon1))
            <p class="text-lg">
                {!! $icon1 !!}
            </p>
        @endif
        @if (!empty($calendar))
            <p class="text-md font-semibold">
                {{ $calendar }}
            </p>
        @endif
    </div>
    <div class="mt-2 flex gap-2 text-gray-600 items-center">
        @if (!empty($icon2))
            <p class="text-lg">
                {!! $icon2 !!}
            </p>
        @endif
        @if (!empty($time))
            <p class="text-md font-semibold">
                {{ $time }}
            </p>
        @endif
    </div>

    <div class="gap-2 mt-5">
        <button class="w-full px-4 py-1 rounded-lg flex justify-center items-center gap-3 bg-[#10AF13] hover:bg-[#0e8e0f] text-white">
            @if (!empty($icon3))
                <p class="text-lg">
                    {!! $icon3 !!}
                </p>
            @endif
            @if (!empty($checkIn))
                <p class="text-md font-semibold text-white">
                    {{ $checkIn }}
                </p>
            @endif
        </button>
    </div>

    <div class="mt-5">
        <div class="w-full px-4 py-2 font-medium rounded-lg flex items-center justify-start bg-blue-100 border border-blue-300 gap-2">
            @if (!empty($info))
                <p class="text-md text-[#0059FF]">
                    {!! $info !!}
                </p>
            @endif
        </div>
    </div>

    <div class="mt-5">
        <div class="w-full px-4 py-2 font-medium rounded-lg flex items-center justify-start bg-orange-100 border border-orange-300 gap-2">
            @if (!empty($icon4))
                <p class="text-md text-[#FF4D00]">
                    {!! $icon4 !!}
                </p>
            @endif
            @if (!empty($info1))
                <p class="text-md text-[#FF4D00]">
                    {!! $info1 !!}
                </p>
            @endif
        </div>
    </div>
</div>