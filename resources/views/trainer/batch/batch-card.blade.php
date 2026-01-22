<!-- resources/views/trainer/batch/batch-card.blade.php -->
<a href="{{ route('trainer.batches.show', $batch['id']) }}" 
   class="bg-white border rounded-2xl p-6 flex flex-col hover:shadow-md transition h-full">
    
    {{-- Header: Title + Status --}}
    <div class="flex justify-between items-start gap-3 mb-2">
        <h1 class="text-black font-bold text-lg line-clamp-2 flex-1">
            {{ $batch['title'] }}
        </h1>
        <span class="px-2.5 py-1 text-xs font-medium rounded-full uppercase {{ badgeStatus($batch['status']) }} flex-shrink-0 whitespace-nowrap">
            {{ $batch['status'] }}
        </span>
    </div>

    {{-- Batch Code --}}
    <p class="text-gray-600 font-medium text-sm mb-3">
        {{ $batch['batch_code'] }}
    </p>

    {{-- Category Badge --}}
    <div class="inline-flex items-center px-3 py-1 w-fit text-xs font-bold rounded-lg border mb-4">
        {{ $batch['category_name'] }}
    </div>

    {{-- Batch Info --}}
    <div class="space-y-2 mb-4">
        {{-- Date --}}
        <div class="flex gap-2 text-gray-600 items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0" width="18" height="18" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                <path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" />
                <path d="M16 3v4" />
                <path d="M8 3v4" />
                <path d="M4 11h16" />
            </svg>
            <p class="text-sm font-medium">
                {{ $batch['start_date'] }}
            </p>
        </div>

        {{-- Time --}}
        <div class="flex gap-2 text-gray-600 items-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0">
                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                <path d="M12 12h-3.5" />
                <path d="M12 7v5" />
            </svg>
            <p class="text-sm font-medium">
                {{ $batch['start_time'] }} - {{ $batch['end_time'] }}
            </p>
        </div>

        {{-- Participants --}}
        <div class="flex gap-2 text-gray-600 items-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0">
                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                <path d="M21 21v-2a4 4 0 0 0 -3 -3.85" />
            </svg>
            <p class="text-sm font-medium">
                {{ $batch['participants_count'] }} peserta
            </p>
        </div>

        {{-- Zoom Link --}}
        @if($batch['zoom_link'])
            <div class="flex gap-2 text-gray-600 items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" class="flex-shrink-0">
                    <path fill="#4B5563" d="m10 17.55l-1.77 1.72a2.47 2.47 0 0 1-3.5-3.5l4.54-4.55a2.46 2.46 0 0 1 3.39-.09l.12.1a1 1 0 0 0 1.4-1.43a2.75 2.75 0 0 0-.18-.21a4.46 4.46 0 0 0-6.09.22l-4.6 4.55a4.48 4.48 0 0 0 6.33 6.33L11.37 19A1 1 0 0 0 10 17.55ZM20.69 3.31a4.49 4.49 0 0 0-6.33 0L12.63 5A1 1 0 0 0 14 6.45l1.73-1.72a2.47 2.47 0 0 1 3.5 3.5l-4.54 4.55a2.46 2.46 0 0 1-3.39.09l-.12-.1a1 1 0 0 0-1.4 1.43a2.75 2.75 0 0 0 .23.21a4.47 4.47 0 0 0 6.09-.22l4.55-4.55a4.49 4.49 0 0 0 .04-6.33Z" />
                </svg>
                <span class="text-sm font-medium text-[#0059FF] hover:underline">
                    Zoom Link
                </span>
            </div>
        @endif
    </div>

    {{-- Divider --}}
    <hr class="border-gray-200 my-3">

    {{-- Stats Row --}}
    <div class="grid grid-cols-2 gap-4 mb-3">
        <div>
            <p class="text-xs font-medium text-gray-600 mb-1">Attendance</p>
            <p class="text-sm font-semibold text-black">
                {{ $batch['attendance_count'] }}/{{ $batch['participants_count'] }}
            </p>
        </div>
        <div>
            <p class="text-xs font-medium text-gray-600 mb-1">Completed</p>
            <p class="text-sm font-semibold text-black">
                {{ $batch['completed_count'] }}/{{ $batch['participants_count'] }}
            </p>
        </div>
    </div>

    {{-- Divider --}}
    <hr class="border-gray-200 my-3">

    {{-- Materials & Tasks --}}
    <div class="flex gap-6">
        <div class="flex items-center gap-2 text-gray-600">
            <p class="text-sm font-medium">Materi:</p>
            <p class="text-sm font-semibold text-black">{{ $batch['total_tasks'] ?? 0 }}</p>
        </div>
        <div class="flex items-center gap-2 text-gray-600">
            <p class="text-sm font-medium">Tugas:</p>
            <p class="text-sm font-semibold text-black">{{ $batch['total_tasks'] }}</p>
        </div>
    </div>
</a>