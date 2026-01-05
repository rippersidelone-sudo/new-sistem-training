<div class="bg-white border rounded-2xl p-6 flex flex-col h-33 hover:shadow-md transition">
    <!-- TOP: Title + Status -->
    <div class="flex justify-between items-start">
        <h1 class="text-black font-bold text-xl">
            {{ $title }}
        </h1>

        <div class="px-3 py-1 text-xs font-medium rounded-full uppercase {{ $colorStatus ?? '' }}">
            {!! $status !!}
        </div>
    </div>

    <!-- Sub Title -->
    <div class="flex items-start mt-1">
        @if (!empty($title2))
            <h2 class="text-gray-600 font-medium text-base">
                {{ $title2 }}
            </h2>
        @endif
    </div>

    @if (!empty($kategori))
        <div class="px-3 py-1 w-fit mt-4 text-xs font-bold rounded-lg border">
            {!! $kategori !!}
        </div>
    @endif

    <!-- BOTTOM: VALUE -->
    <div class="mt-5 flex gap-2 text-gray-600 items-center">
        <p class="text-lg">
            {!! $icon !!}
        </p>
        <p class="text-md font-medium">
            {{ $calendar }}
        </p>
    </div>
    <div class="mt-2 flex gap-2 text-gray-600 items-center">
        @if (!empty($icon1))
            <p class="text-lg">
                {!! $icon1 !!}
            </p>
        @endif
        <p class="text-md font-medium">
            {{ $time }}
        </p>
    </div>
    <div class="mt-2 flex gap-2 text-gray-600 items-center">
        @if (!empty($icon2))
            <p class="text-lg">
                {!! $icon2 !!}
            </p>
        @endif
        <p class="text-md font-medium">
            {{ $peserta }}
        </p>
    </div>
    <div class="mt-2 flex gap-2 text-gray-600 items-center">
        @if (!empty($icon3))
            <p class="text-lg">
                {!! $icon3 !!}
            </p>
        @endif
        <a href="#" class="text-md font-medium text-[#0059FF] hover:underline">
            {{ $link }}
        </a>
    </div>

    <hr class="border-gray-200 mt-3">

    <div class="flex items-start gap-20">
        <div class="mt-2">
            @if (!empty($attendance))
                <h2 class="text-md font-medium text-gray-600">
                    {{ $attendance }}
                </h2>
            @endif
            @if (!empty($value))
                <p class="text-md font-medium text-black">
                    {{ $value }}
                </p>
            @endif
        </div>
        <div class="mt-2">
            @if (!empty($completed))
                <h2 class="text-md font-medium text-gray-600">
                    {{ $completed }}
                </h2>
            @endif
            @if (!empty($value1))
                <p class="text-md font-medium text-black">
                    {{ $value1 }}
                </p>
            @endif
        </div>
    </div>

    <hr class="border-gray-200 mt-3">

    <div class="mt-2 text-gray-600 flex gap-2 items-center">
        @if (!empty($materi))
            <h2 class="text-md font-medium">
                {{ $materi }}
            </h2>
        @endif
        <p class="text-md font-medium">
            {{ $value2 }}
        </p>
    </div>

    <div class="mt-2 text-gray-600 flex gap-2 items-center">
        @if (!empty($tugas))
            <h2 class="text-md font-medium">
                {{ $tugas }}
            </h2>
        @endif
        <p class="text-md font-medium">
            {{ $value3 }}
        </p>
    </div>
</div>