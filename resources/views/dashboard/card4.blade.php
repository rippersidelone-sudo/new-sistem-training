<div class="bg-white border rounded-2xl p-6 flex flex-col h-33 hover:shadow-md transition">
    <!-- TOP: Title + Status -->
    <div class="flex justify-between items-start">
        <h1 class="text-black font-medium text-xl">
            {{ $title }}
        </h1>
    </div>

    <!-- Sub Title -->
    <div class="flex items-start mt-2">
        @if (!empty($status))
            <div class="px-3 py-1 text-xs font-medium rounded-full {{ $colorStatus ?? '' }}">
                {!! $status !!}
            </div>
        @endif
    </div>

    @if (!empty($keterangan))
        <div class="mt-7 text-md font-medium text-gray-600">
            {!! $keterangan !!}
        </div>
    @endif

    <!-- BOTTOM: VALUE -->
    <div class="mt-5 flex gap-2 text-gray-600 items-center">
        <p class="text-lg">
            {!! $icon !!}
        </p>
        @if (!empty($kategori))
            <p class="text-md font-semibold">
                {{ $kategori }}
            </p>
        @endif
    </div>
    <div class="mt-2 flex gap-2 text-gray-600 items-center">
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
    <div class="mt-2 flex gap-2 text-gray-600 items-center">
        @if (!empty($icon3))
            <p class="text-lg">
                {!! $icon3 !!}
            </p>
        @endif
        @if (!empty($peserta))
            <p href="#" class="text-md font-semibold">
                {{ $peserta }}
            </p>
        @endif
    </div>
    <div class="mt-2 flex gap-2 text-gray-600 items-center">
        @if (!empty($icon4))
            <p class="text-lg">
                {!! $icon4 !!}
            </p>
        @endif
        @if (!empty($trainer))
            <p href="#" class="text-md font-semibold">
                {{ $trainer }}
            </p>
        @endif
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-1 mt-6">
        <div class="w-full px-4 py-2 font-medium rounded-lg flex items-center justify-start bg-orange-100 border border-orange-300 gap-2">
            @if (!empty($icon5))
                <p class="text-md text-[#FF4D00]">
                    {!! $icon5 !!}
                </p>
            @endif
            @if (!empty($info))
                <p class="text-md text-[#FF4D00]">
                    {!! $info !!}
                </p>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-6">
        <!-- Button 1 -->
        <button class="w-full px-4 py-1 border rounded-lg flex flex-col items-center justify-center hover:bg-gray-100">
            @if (!empty($value))
                <p class="text-md font-semibold text-black">
                    {{ $value }}
                </p>
            @endif
        </button>

        <!-- Button 2 -->
        <button class="w-full px-4 rounded-lg flex flex-col items-center justify-center {{ $colorButton ?? '' }}">
            @if (!empty($value1))
                <p class="text-md font-semibold text-white">
                    {!! $value1 !!}
                </p>
            @endif
        </button>
    </div>
</div>