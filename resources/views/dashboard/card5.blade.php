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
            @if (!empty($statusPendaftaran))
                <h2 class="text-md font-medium">
                    {{ $statusPendaftaran }}
                </h2>
            @endif
            <div class="px-3 py-1 w-fit text-xs uppercase font-medium rounded-full {{ $colorStatusPendaftaran ?? '' }}">
                @if (!empty($value1))
                    {!! $value1 !!}
                @endif
            </div>
        </div>
    </div>

    <!-- BOTTOM: VALUE -->
    <div class="mt-7 flex gap-2 text-gray-600 items-center">
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

    <hr class="border-gray-200 mt-4">

    <div class="flex items-start gap-20 mt-3">
        <div>
            @if (!empty($materi))
                <h2 class="text-md font-medium text-gray-600">
                    {{ $materi }}
                </h2>
            @endif
            @if (!empty($value2))
                <p class="text-md font-semibold text-black">
                    {{ $value2 }}
                </p>
            @endif
        </div>
        <div>
            @if (!empty($tugas))
                <h2 class="text-md font-medium text-gray-600">
                    {{ $tugas }}
                </h2>
            @endif
            @if (!empty($value3))
                <p class="text-md font-semibold text-black">
                    {{ $value3 }}
                </p>
            @endif
        </div>
    </div>

    <div class="mt-4 text-gray-600 gap-2 items-center">
        @if (!empty($kehadiran))
            <h2 class="text-md font-medium">
                {{ $kehadiran }}
            </h2>
        @endif
        <div class="px-3 py-1 w-fit text-xs uppercase font-medium rounded-full {{ $colorStatus1 ?? '' }}">
            {!! $status1 !!}
        </div>
    </div>

    <div class="gap-2 mt-6">
        <button class="w-full px-4 py-1 border rounded-lg flex justify-center items-center gap-3 hover:bg-gray-100" @click="trainingDetail = true">
            @if (!empty($icon3))
                <p class="text-lg">
                    {!! $icon3 !!}
                </p>
            @endif
            @if (!empty($detail))
                <p class="text-md font-semibold text-black">
                    {{ $detail }}
                </p>
            @endif
        </button>
    </div>
</div>