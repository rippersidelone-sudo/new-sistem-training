@extends('layouts.app')

@section('content')
    <div class="px-2">
        <h1 class="text-2xl font-semibold">Batch Oversight</h1>
        <p class="text-[#737373] mt-2 font-medium">Monitor dan kelola semua batch pelatihan</p>
    </div>

    <!-- Filter Form -->
    <form method="GET" action="{{ route('admin.batch-oversight.index') }}" id="filterForm">
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
                <input type="text" name="search" value="{{ request('search') }}"
                    class="w-full border-0 focus:ring-0 text-sm bg-[#F1F1F1] placeholder-[#737373]"
                    placeholder="Cari batch pelatihan..." />
            </div>

            <!-- Dropdown Status -->
            <div x-data="{ 
                open: false, 
                value: '{{ request('status', '') }}', 
                label: '{{ request('status') ? ucfirst(request('status')) : 'Semua Status' }}' 
            }" class="relative w-full">
                <button type="button" @click="open = !open"
                    :class="open ? 'border-[#10AF13] ring-1 ring-[#10AF13]' : 'border-gray-300'"
                    class="w-full px-3 py-2 rounded-lg border cursor-pointer flex justify-between items-center text-sm bg-white transition">
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

                    <template
                        x-for="item in [
                            { value: '', label: 'Semua Status' },
                            { value: 'Scheduled', label: 'Scheduled' },
                            { value: 'Ongoing', label: 'Ongoing' },
                            { value: 'Completed', label: 'Completed' }
                        ]"
                        :key="item.value">
                        <div @click="value = item.value; label = item.label; open = false; $refs.statusInput.value = value; document.getElementById('filterForm').submit();"
                            class="px-3 py-2 text-sm cursor-pointer flex justify-between items-center hover:bg-gray-100">
                            <span x-text="item.label"></span>
                            <svg x-show="value === item.value" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" 
                                stroke="#10AF13" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                                class="icon icon-tabler icons-tabler-outline icon-tabler-check">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M5 12l5 5l10 -10" />
                            </svg>
                        </div>
                    </template>
                </div>

                <input type="hidden" name="status" x-ref="statusInput" :value="value">
            </div>

            <!-- Dropdown Cabang -->
            <div x-data="{ 
                open: false, 
                value: '{{ request('branch_id', '') }}', 
                label: '{{ request('branch_id') ? $branches->firstWhere('id', request('branch_id'))->name ?? 'Semua Cabang' : 'Semua Cabang' }}' 
            }" class="relative w-full">
                <button type="button" @click="open = !open"
                    :class="open ? 'border-[#10AF13] ring-1 ring-[#10AF13]' : 'border-gray-300'"
                    class="w-full px-3 py-2 rounded-lg border cursor-pointer flex justify-between items-center text-sm bg-white transition">
                    <span x-text="label"></span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                        stroke="#374151" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="icon icon-tabler icons-tabler-outline icon-tabler-chevron-down">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M6 9l6 6l6 -6" />
                    </svg>
                </button>

                <div x-show="open" @click.outside="open = false" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="absolute z-20 mt-2 w-full bg-white border rounded-lg shadow-md overflow-hidden">

                    <div @click="value = ''; label = 'Semua Cabang'; open = false; $refs.branchInput.value = ''; document.getElementById('filterForm').submit();"
                        class="px-3 py-2 text-sm cursor-pointer flex justify-between items-center hover:bg-gray-100">
                        <span>Semua Cabang</span>
                        <svg x-show="value === ''" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" 
                            stroke="#10AF13" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M5 12l5 5l10 -10" />
                        </svg>
                    </div>

                    @foreach($branches as $branch)
                    <div @click="value = '{{ $branch->id }}'; label = '{{ $branch->name }}'; open = false; $refs.branchInput.value = value; document.getElementById('filterForm').submit();"
                        class="px-3 py-2 text-sm cursor-pointer flex justify-between items-center hover:bg-gray-100">
                        <span>{{ $branch->name }}</span>
                        <svg x-show="value === '{{ $branch->id }}'" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" 
                            stroke="#10AF13" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M5 12l5 5l10 -10" />
                        </svg>
                    </div>
                    @endforeach
                </div>

                <input type="hidden" name="branch_id" x-ref="branchInput" :value="value">
            </div>
        </div>
    </form>

    <div class="grid gap-6 mt-8 px-2">
        <div class="bg-white border rounded-2xl p-6">
            <div class="flex items-center justify-between position-relative w-full mb-5">
                <h2 class="text-lg font-semibold">
                    Daftar Batch Pelatihan
                </h2>
                <a href="{{ route('admin.batch-oversight.export', request()->query()) }}"
                    class="flex items-center bg-white border rounded-lg px-3 gap-3 py-1 w-fit cursor-pointer hover:bg-gray-50 transition font-semibold">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="icon icon-tabler icons-tabler-outline icon-tabler-download">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
                        <path d="M7 11l5 5l5 -5" />
                        <path d="M12 4l0 12" />
                    </svg>
                    <span>Export CSV</span>
                </a>
            </div>
            <div class="overflow-x-auto" x-data="{ openDetail: false, selectedBatch: null }">
                @if($batches->isEmpty())
                <div class="text-center py-12 text-gray-500">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="1.5" class="mx-auto mb-4 text-gray-400">
                        <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <p class="font-medium">Tidak ada data batch pelatihan</p>
                    <p class="text-sm mt-1">Batch akan muncul di sini setelah Coordinator membuat batch baru</p>
                </div>
                @else
                <table class="min-w-full border border-gray-200 rounded-xl overflow-hidden">
                    <thead class="bg-[#F1F1F1]">
                        <tr class="text-left text-sm font-semibold text-gray-700">
                            <th class="px-4 py-3">Kode</th>
                            <th class="px-4 py-3">Judul Batch</th>
                            <th class="px-4 py-3">Trainer</th>
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3 text-center">Peserta</th>
                            <th class="px-4 py-3 text-center">Status</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y text-sm">
                        @foreach($batches as $batch)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3 font-medium">{{ formatBatchCode($batch->id, $batch->created_at->year) }}</td>
                            <td class="px-4 py-3">{{ $batch->title }}</td>
                            <td class="px-4 py-3">{{ $batch->trainer->name ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $batch->start_date->format('d M Y') }}</td>
                            <td class="px-4 py-3 text-center">
                                {{ $batch->participants_count }}/<span class="text-gray-700">Lulus: {{ $batch->passed_count ?? 0 }}</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @php
                                    $statusConfig = [
                                        'Scheduled' => ['bg' => 'bg-blue-100', 'text' => 'text-[#0059FF]'],
                                        'Ongoing' => ['bg' => 'bg-green-100', 'text' => 'text-[#10AF13]'],
                                        'Completed' => ['bg' => 'bg-orange-100', 'text' => 'text-[#FF4D00]'],
                                    ];
                                    $config = $statusConfig[$batch->status] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-700'];
                                @endphp
                                <span class="px-3 py-1 text-xs font-medium rounded-full {{ $config['bg'] }} {{ $config['text'] }} uppercase">
                                    {{ $batch->status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex justify-center gap-2">
                                    <button @click="openDetail = true; fetchBatchDetail({{ $batch->id }})"
                                        class="hover:text-[#10AF13] transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                            viewBox="0 0 20 20" fill="currentColor">
                                            <path
                                                d="M10 3C5 3 1.73 7.11 1.07 10c.66 2.89 4 7 8.93 7s8.27-4.11 8.93-7C18.27 7.11 15 3 10 3zM10 15a5 5 0 110-10 5 5 0 010 10z" />
                                            <path d="M10 7a3 3 0 100 6 3 3 0 000-6z" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif

                <!-- Modal Detail -->
                <div x-show="openDetail" x-cloak x-transition id="detailBatch"
                    class="fixed inset-0 bg-black/40 z-50 items-center flex justify-center">
                    <div @click.outside="openDetail = false" class="bg-white max-w-xl rounded-2xl shadow-lg p-8 relative">

                        <!-- Close Button -->
                        <button @click="openDetail = false"
                            class="absolute top-6 right-6 text-[#737373] hover:text-black text-xl">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-x">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M18 6l-12 12" />
                                <path d="M6 6l12 12" />
                            </svg>
                        </button>

                        <!-- Header -->
                        <h2 class="text-xl font-semibold">Detail Batch Pelatihan</h2>
                        <p class="text-[#737373] mb-6">Informasi lengkap batch pelatihan</p>

                        <!-- Loading State -->
                        <div x-show="!selectedBatch" class="text-center py-8">
                            <svg class="animate-spin h-8 w-8 mx-auto text-[#10AF13]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <p class="text-gray-500 mt-2">Memuat data...</p>
                        </div>

                        <!-- Content -->
                        <div x-show="selectedBatch" class="bg-gray-50 rounded-xl p-6 grid grid-cols-2 gap-y-6 gap-x-10">
                            <div>
                                <p class="text-gray-700 text-md font-medium">Kode</p>
                                <p x-text="selectedBatch?.code || '-'"></p>
                            </div>
                            <div>
                                <p class="text-gray-700 text-md font-medium">Judul Batch</p>
                                <p x-text="selectedBatch?.title || '-'"></p>
                            </div>
                            <div>
                                <p class="text-gray-700 text-md font-medium">Kategori</p>
                                <p x-text="selectedBatch?.category?.name || '-'"></p>
                            </div>
                            <div>
                                <p class="text-gray-700 text-md font-medium">Trainer</p>
                                <p x-text="selectedBatch?.trainer?.name || '-'"></p>
                            </div>
                            <div>
                                <p class="text-gray-700 text-md font-medium">Tanggal Mulai</p>
                                <p x-text="selectedBatch?.start_date || '-'"></p>
                            </div>
                            <div>
                                <p class="text-gray-700 text-md font-medium">Tanggal Selesai</p>
                                <p x-text="selectedBatch?.end_date || '-'"></p>
                            </div>
                            <div>
                                <p class="text-gray-700 text-md font-medium">Peserta</p>
                                <p>
                                    <span x-text="selectedBatch?.participants_count || 0"></span>/
                                    <span class="text-gray-700">Lulus: <span x-text="selectedBatch?.passed_count || 0"></span></span>
                                </p>
                            </div>
                            <div>
                                <p class="text-gray-700 text-md font-medium">Status</p>
                                <span
                                    class="inline-block px-2 py-1 uppercase text-xs font-medium rounded-full"
                                    :class="{
                                        'bg-blue-100 text-[#0059FF]': selectedBatch?.status === 'Scheduled',
                                        'bg-green-100 text-[#10AF13]': selectedBatch?.status === 'Ongoing',
                                        'bg-orange-100 text-[#FF4D00]': selectedBatch?.status === 'Completed'
                                    }"
                                    x-text="selectedBatch?.status || '-'">
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function fetchBatchDetail(batchId) {
            fetch(`/admin/batch-oversight/${batchId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Alpine.store('batchDetail', data.batch);
                        // Update selectedBatch in the component
                        const component = document.querySelector('[x-data]').__x.$data;
                        component.selectedBatch = {
                            code: formatBatchCode(data.batch.id, new Date(data.batch.created_at).getFullYear()),
                            title: data.batch.title,
                            category: data.batch.category,
                            trainer: data.batch.trainer,
                            start_date: new Date(data.batch.start_date).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }),
                            end_date: new Date(data.batch.end_date).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }),
                            participants_count: data.batch.batch_participants_count || 0,
                            passed_count: data.batch.passed_count || 0,
                            status: data.batch.status
                        };
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Gagal memuat detail batch');
                });
        }

        function formatBatchCode(id, year) {
            return `TRN-${year}-${String(id).padStart(3, '0')}`;
        }

        // Auto-submit search on Enter key
        document.querySelector('input[name="search"]').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('filterForm').submit();
            }
        });
    </script>
@endsection