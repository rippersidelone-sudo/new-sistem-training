@extends('layouts.app')

@section('content')
    <div class="px-2">
        <h1 class="text-2xl font-semibold">Audit Log</h1>
        <p class="text-[#737373] mt-2 font-medium">Riwayat aktivitas sistem dan perubahan data</p>
    </div>

    @php
        $roleItems = $roles
            ->map(function ($role) {
                return [
                    'value' => (string) $role->id,
                    'label' => $role->description,
                ];
            })
            ->values();
    @endphp

    <script>
        window.roleItems = @json($roleItems);
    </script>

    <div class="grid grid-cols-1 border lg:grid-cols-3 gap-4 px-5 bg-white py-6 rounded-2xl mt-8 mx-2">
        <!-- Search -->
        <div class="flex items-center bg-[#F1F1F1] rounded-lg px-3">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="text-[#737373]">
                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
                <path d="M21 21l-6 -6" />
            </svg>
            <input type="text" name="search"
                class="w-full border-0 focus:ring-0 text-sm bg-[#F1F1F1] placeholder-[#737373]"
                placeholder="Cari user atau detail..." />
        </div>

        <!-- Dropdown Aksi -->
        <div x-data="{ open: false, value: '', label: 'Semua Aksi' }" class="relative w-full">
            <button @click="open = !open"
                :class="open
                    ?
                    'border-[#10AF13] ring-1 ring-[#10AF13]' :
                    'border-gray-300'"
                class="w-full px-3 py-2 rounded-lg border cursor-pointer
                flex justify-between items-center text-sm bg-white transition">
                <span x-text="label"></span>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                    stroke="#374151" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="icon icon-tabler icons-tabler-outline icon-tabler-chevron-down">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M6 9l6 6l6 -6" />
                </svg>
            </button>

            <!-- Dropdown Content -->
            <div x-show="open" @click.outside="open = false" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="absolute z-20 mt-2 w-full bg-white border rounded-lg shadow-md overflow-hidden">

                <!-- Item -->
                <template
                    x-for="item in [
                        { value: '', label: 'Semua Aksi' },
                        { value: 'create', label: 'Create' },
                        { value: 'update', label: 'Update' },
                        { value: 'delete', label: 'Delete' },
                        { value: 'approve', label: 'Approve' },
                        { value: 'reject', label: 'Reject' }
                    ]"
                    :key="item.value">

                    <div @click="value = item.value; label = item.label; open = false"
                        class="px-3 py-2 text-sm cursor-pointer flex justify-between items-center hover:bg-gray-100">

                        <span x-text="item.label"></span>

                        <!-- Check Icon -->
                        <svg x-show="value === item.value" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                            viewBox="0 0 24 24" fill="none" stroke="#10AF13" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-check">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M5 12l5 5l10 -10" />
                        </svg>
                    </div>
                </template>
            </div>

            <!-- Hidden input untuk backend -->
            <input type="hidden" name="cabang" :value="value">
        </div>

        <!-- Dropdown Entitas -->
        <div x-data="{
            open: false,
            value: '',
            label: 'Semua Entitas',
            items: [
                { value: '', label: 'Semua Entitas' },
                ...window.roleItems
            ]
        }" class="relative w-full">
            <button @click="open = !open"
                :class="open
                    ?
                    'border-[#10AF13] ring-1 ring-[#10AF13]' :
                    'border-gray-300'"
                class="w-full px-3 py-2 rounded-lg border cursor-pointer
                flex justify-between items-center text-sm bg-white transition">
                <span x-text="label"></span>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                    stroke="#374151" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="icon icon-tabler icons-tabler-outline icon-tabler-chevron-down">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M6 9l6 6l6 -6" />
                </svg>
            </button>

            <!-- Dropdown Content -->
            <div x-show="open" @click.outside="open = false" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="absolute z-20 mt-2 w-full bg-white border rounded-lg shadow-md overflow-hidden">

                <!-- Item -->
                <template x-for="item in items" :key="item.value">
                    <div @click="value = item.value; label = item.label; open = false"
                        class="px-3 py-2 text-sm cursor-pointer flex justify-between items-center hover:bg-gray-100">

                        <span x-text="item.label"></span>

                        <!-- Check Icon -->
                        <svg x-show="value === item.value" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                            viewBox="0 0 24 24" fill="none" stroke="#10AF13" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-check">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M5 12l5 5l10 -10" />
                        </svg>
                    </div>
                </template>
            </div>

            <!-- Hidden input untuk backend -->
            <input type="hidden" name="role" :value="value">
        </div>
    </div>

    <!-- Riwayat Aktivitas -->
    <div class="bg-white border rounded-2xl p-6 mt-8 mx-2">
        <h2 class="text-lg font-semibold mb-5">
            Riwayat Aktivitas <span>(6)</span>
        </h2>
        <div class="space-y-4 max-h-[1000px] overflow-y-auto pr-1">
            <!-- ITEM 1 -->
            <div class="px-4 py-3 border rounded-xl hover:bg-gray-50 transition">
                <div class="flex items-center gap-3">
                    <div class="rounded-xl bg-gray-200 px-3 w-fit py-1">
                        <p class="text-sm font-bold text-gray-800">
                            UPDATE_BATCH
                        </p>
                    </div>
                    <div class="rounded-xl border px-3 w-fit py-1">
                        <p class="text-sm font-bold">
                            Training Coordinator
                        </p>
                    </div>
                    <div>
                        <p class="text-lg font-medium">
                            Koordinator Pelatihan
                        </p>
                    </div>
                    <div class="ml-auto text-right">
                        <p class="px-3 py-1 text-md font-medium">
                            2 Des 2025, 11.53
                        </p>
                    </div>
                </div>
                <div class="mt-1">
                    <p class="text-lg font-medium text-gray-700">
                        Update batch: Python Game Developer Batch 1
                    </p>
                </div>
            </div>

            <!-- ITEM 2 -->
            <div class="px-4 py-3 border rounded-xl hover:bg-gray-50 transition">
                <div class="flex items-center gap-3">
                    <div class="rounded-xl bg-gray-200 px-3 w-fit py-1">
                        <p class="text-sm font-bold text-gray-800">
                            CREATE_BATCH
                        </p>
                    </div>
                    <div class="rounded-xl border px-3 w-fit py-1">
                        <p class="text-sm font-bold">
                            Training Coordinator
                        </p>
                    </div>
                    <div>
                        <p class="text-lg font-medium">
                            Koordinator Pelatihan
                        </p>
                    </div>
                    <div class="ml-auto text-right">
                        <p class="px-3 py-1 text-md font-medium">
                            1 Nov 2025, 18.30
                        </p>
                    </div>
                </div>
                <div class="mt-1">
                    <p class="text-lg font-medium text-gray-700">
                        Membuat batch baru: Python Game Developer Batch 1
                    </p>
                </div>
            </div>

            <!-- ITEM 3 -->
            <div class="px-4 py-3 border rounded-xl hover:bg-gray-50 transition">
                <div class="flex items-center gap-3">
                    <div class="rounded-xl bg-gray-200 px-3 w-fit py-1">
                        <p class="text-sm font-bold text-gray-800">
                            UPDATE_BATCH_STATUS
                        </p>
                    </div>
                    <div class="rounded-xl border px-3 w-fit py-1">
                        <p class="text-sm font-bold">
                            Training Coordinator
                        </p>
                    </div>
                    <div>
                        <p class="text-lg font-medium">
                            Koordinator Pelatihan
                        </p>
                    </div>
                    <div class="ml-auto text-right">
                        <p class="px-3 py-1 text-md font-medium">
                            10 Nov 2025, 16.00
                        </p>
                    </div>
                </div>
                <div class="mt-1">
                    <p class="text-lg font-medium text-gray-700">
                        Mengubah status batch menjadi ONGOING
                    </p>
                </div>
            </div>

            <!-- ITEM 4 -->
            <div class="px-4 py-3 border rounded-xl hover:bg-gray-50 transition">
                <div class="flex items-center gap-3">
                    <div class="rounded-xl bg-gray-200 px-3 w-fit py-1">
                        <p class="text-sm font-bold text-gray-800">
                            APPROVE_PARTICIPANT
                        </p>
                    </div>
                    <div class="rounded-xl border px-3 w-fit py-1">
                        <p class="text-sm font-bold">
                            Branch PIC
                        </p>
                    </div>
                    <div>
                        <p class="text-lg font-medium">
                            PIC Jakarta
                        </p>
                    </div>
                    <div class="ml-auto text-right">
                        <p class="px-3 py-1 text-md font-medium">
                            21 Okt 2025, 22.20
                        </p>
                    </div>
                </div>
                <div class="mt-1">
                    <p class="text-lg font-medium text-gray-700">
                        Menyetujui pendaftaran peserta: Guru Peserta
                    </p>
                </div>
            </div>

            <!-- ITEM 5 -->
            <div class="px-4 py-3 border rounded-xl hover:bg-gray-50 transition">
                <div class="flex items-center gap-3">
                    <div class="rounded-xl bg-gray-200 px-3 w-fit py-1">
                        <p class="text-sm font-bold text-gray-800">
                            VALIDATE_ATTENDANCE
                        </p>
                    </div>
                    <div class="rounded-xl border px-3 w-fit py-1">
                        <p class="text-sm font-bold">
                            Trainer
                        </p>
                    </div>
                    <div>
                        <p class="text-lg font-medium">
                            Ahmad
                        </p>
                    </div>
                    <div class="ml-auto text-right">
                        <p class="px-3 py-1 text-md font-medium">
                            10 Nov 2025, 17.05
                        </p>
                    </div>
                </div>
                <div class="mt-1">
                    <p class="text-lg font-medium text-gray-700">
                        Validasi kehadiran peserta: Guru Peserta
                    </p>
                </div>
            </div>

            <!-- ITEM 6 -->
            <div class="px-4 py-3 border rounded-xl hover:bg-gray-50 transition">
                <div class="flex items-center gap-3">
                    <div class="rounded-xl bg-gray-200 px-3 w-fit py-1">
                        <p class="text-sm font-bold text-gray-800">
                            SUBMIT_ASSIGNMENT
                        </p>
                    </div>
                    <div class="rounded-xl border px-3 w-fit py-1">
                        <p class="text-sm font-bold">
                            Participant
                        </p>
                    </div>
                    <div>
                        <p class="text-lg font-medium">
                            Guru Peserta
                        </p>
                    </div>
                    <div class="ml-auto text-right">
                        <p class="px-3 py-1 text-md font-medium">
                            12 Nov 2025, 22.30
                        </p>
                    </div>
                </div>
                <div class="mt-1">
                    <p class="text-lg font-medium text-gray-700">
                        Submit tugas: Game Sederhana dengan Pygame
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
