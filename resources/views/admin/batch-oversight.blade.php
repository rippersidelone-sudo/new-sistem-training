@extends('layouts.admin')

@section('content')
    <div class="px-2">
        <h1 class="text-2xl font-semibold">Batch Oversight</h1>
        <p class="text-[#737373] mt-2 font-medium">Monitor dan kelola semua batch pelatihan</p>
    </div>

    {{-- Filter Bar Component --}}
    <div class="mt-8 mx-2">
        <x-filter-bar
            :action="route('admin.batch-oversight.index')"
            searchPlaceholder="Cari batch pelatihan..."
            :filters="[
                [
                    'name' => 'status',
                    'placeholder' => 'Semua Status',
                    'options' => [
                        ['value' => '', 'label' => 'Semua Status'],
                        ['value' => 'Scheduled', 'label' => 'Scheduled'],
                        ['value' => 'Ongoing', 'label' => 'Ongoing'],
                        ['value' => 'Completed', 'label' => 'Completed'],
                    ]
                ],
                [
                    'name' => 'branch_id',
                    'placeholder' => 'Semua Cabang',
                    'options' => array_merge(
                        [['value' => '', 'label' => 'Semua Cabang']],
                        $branches->map(fn($b) => ['value' => $b->id, 'label' => $b->name])->toArray()
                    )
                ]
            ]"
        />
    </div>

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

            <!-- Bagian tabel dan modal -->
            <div class="overflow-x-auto" x-data="{
                openDetail: false,
                selectedBatch: null,
               
                fetchBatchDetail(batchId) {
                    this.selectedBatch = null;
                   
                    fetch(`/admin/batch-oversight/${batchId}`)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                this.selectedBatch = {
                                    code: this.formatBatchCode(data.batch.id, new Date(data.batch.created_at).getFullYear()),
                                    title: data.batch.title,
                                    category: data.batch.category,
                                    trainer: data.batch.trainer,
                                    start_date: new Date(data.batch.start_date).toLocaleDateString('id-ID', {
                                        day: 'numeric',
                                        month: 'short',
                                        year: 'numeric',
                                        hour: '2-digit',
                                        minute: '2-digit'
                                    }),
                                    end_date: new Date(data.batch.end_date).toLocaleDateString('id-ID', {
                                        day: 'numeric',
                                        month: 'short',
                                        year: 'numeric',
                                        hour: '2-digit',
                                        minute: '2-digit'
                                    }),
                                    participants_count: data.batch.participants_count || 0,
                                    passed_count: data.batch.passed_count || 0,
                                    status: data.batch.status,
                                    zoom_link: data.batch.zoom_link
                                };
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Gagal memuat detail batch');
                            this.openDetail = false;
                        });
                },
               
                formatBatchCode(id, year) {
                    return `TRN-${year}-${String(id).padStart(3, '0')}`;
                }
            }">
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
                    <div @click.outside="openDetail = false" class="bg-white max-w-2xl w-full mx-4 rounded-2xl shadow-lg p-8 relative max-h-[90vh] overflow-y-auto">

                        <!-- Close Button -->
                        <button @click="openDetail = false"
                            class="absolute top-6 right-6 text-[#737373] hover:text-black text-xl">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
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
                        <div x-show="selectedBatch" class="space-y-6">
                            <!-- Basic Info -->
                            <div class="bg-gray-50 rounded-xl p-6 grid grid-cols-2 gap-y-6 gap-x-10">
                                <div>
                                    <p class="text-gray-700 text-sm font-medium mb-1">Kode Batch</p>
                                    <p class="font-semibold" x-text="selectedBatch?.code || '-'"></p>
                                </div>
                                <div>
                                    <p class="text-gray-700 text-sm font-medium mb-1">Status</p>
                                    <span
                                        class="inline-block px-3 py-1 uppercase text-xs font-medium rounded-full"
                                        :class="{
                                            'bg-blue-100 text-[#0059FF]': selectedBatch?.status === 'Scheduled',
                                            'bg-green-100 text-[#10AF13]': selectedBatch?.status === 'Ongoing',
                                            'bg-orange-100 text-[#FF4D00]': selectedBatch?.status === 'Completed'
                                        }"
                                        x-text="selectedBatch?.status || '-'">
                                    </span>
                                </div>
                                <div class="col-span-2">
                                    <p class="text-gray-700 text-sm font-medium mb-1">Judul Batch</p>
                                    <p class="font-semibold" x-text="selectedBatch?.title || '-'"></p>
                                </div>
                                <div>
                                    <p class="text-gray-700 text-sm font-medium mb-1">Kategori</p>
                                    <p x-text="selectedBatch?.category?.name || '-'"></p>
                                </div>
                                <div>
                                    <p class="text-gray-700 text-sm font-medium mb-1">Trainer</p>
                                    <p x-text="selectedBatch?.trainer?.name || '-'"></p>
                                </div>
                                <div>
                                    <p class="text-gray-700 text-sm font-medium mb-1">Tanggal Mulai</p>
                                    <p x-text="selectedBatch?.start_date || '-'"></p>
                                </div>
                                <div>
                                    <p class="text-gray-700 text-sm font-medium mb-1">Tanggal Selesai</p>
                                    <p x-text="selectedBatch?.end_date || '-'"></p>
                                </div>
                            </div>

                            <!-- Statistics -->
                            <div class="grid grid-cols-3 gap-4">
                                <div class="bg-blue-50 rounded-lg p-4 text-center">
                                    <p class="text-sm text-gray-600 mb-1">Total Peserta</p>
                                    <p class="text-2xl font-bold text-[#0059FF]" x-text="selectedBatch?.participants_count || 0"></p>
                                </div>
                                <div class="bg-green-50 rounded-lg p-4 text-center">
                                    <p class="text-sm text-gray-600 mb-1">Peserta Lulus</p>
                                    <p class="text-2xl font-bold text-[#10AF13]" x-text="selectedBatch?.passed_count || 0"></p>
                                </div>
                                <div class="bg-orange-50 rounded-lg p-4 text-center">
                                    <p class="text-sm text-gray-600 mb-1">Tingkat Kelulusan</p>
                                    <p class="text-2xl font-bold text-[#FF4D00]">
                                        <span x-text="selectedBatch?.participants_count > 0 ? 
                                            Math.round((selectedBatch?.passed_count / selectedBatch?.participants_count) * 100) : 0"></span>%
                                    </p>
                                </div>
                            </div>

                            <!-- Zoom Link -->
                            <div class="bg-gray-50 rounded-xl p-4" x-show="selectedBatch?.zoom_link">
                                <p class="text-gray-700 text-sm font-medium mb-2">Zoom Link</p>
                                <div class="flex items-center gap-2">
                                    <input type="text" 
                                        :value="selectedBatch?.zoom_link || 'Tidak ada link'"
                                        readonly
                                        class="flex-1 px-3 py-2 bg-white border rounded-lg text-sm">
                                    <button 
                                        @click="navigator.clipboard.writeText(selectedBatch?.zoom_link); alert('Link berhasil dicopy!')"
                                        class="px-3 py-2 bg-[#10AF13] text-white rounded-lg text-sm hover:bg-[#0e8e0f] transition">
                                        Copy
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection