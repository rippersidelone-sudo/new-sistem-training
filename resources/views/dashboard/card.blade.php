<div class="bg-white border rounded-2xl p-6 flex flex-col justify-between h-32">

    <!-- TOP: TITLE + ICON -->
    <div class="flex justify-between items-start">
        <p class="text-gray-700 font-medium text-base">
            {{ $title }}
        </p>

        <div class="{{ $color ?? '' }}">
            {!! $icon !!}
        </div>
    </div>

    <!-- BOTTOM: VALUE -->
    <div>
        <h2 class="text-xl font-semibold {{ $color ?? '' }}">
            {{ $value }}
        </h2>
    </div>

</div>
