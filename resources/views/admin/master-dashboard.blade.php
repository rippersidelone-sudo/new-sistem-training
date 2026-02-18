{{-- resources/views/admin/master-dashboard.blade.php --}}
@extends('layouts.admin')

@section('content')
    {{-- HEADER --}}
    <div class="px-2">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold">Master Dashboard</h1>
                <p class="text-[#737373] mt-2 font-medium">Overview semua batch dan cabang pelatihan</p>
                <p class="text-sm text-[#10AF13] font-medium mt-1">
                    📅 Periode: {{ $dateRange['label'] }}
                </p>
            </div>

            {{-- (Opsional) Sync Button --}}
            <button
                x-data="syncAllAdmin()"
                @click="sync()"
                :disabled="loading"
                :title="loading ? 'Syncing...' : 'Sync semua data dari API'"
                class="w-full sm:w-auto flex items-center justify-center gap-2 px-4 py-2 bg-[#10AF13] text-white rounded-lg hover:bg-[#0e8e0f] transition font-semibold disabled:opacity-60 disabled:cursor-not-allowed">
                <svg x-show="!loading" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4" />
                    <path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4" />
                </svg>
                <svg x-show="loading" x-cloak xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2" class="animate-spin">
                    <path d="M12 3a9 9 0 1 0 9 9" />
                </svg>
                <span x-text="loading ? 'Syncing...' : 'Sync Data'"></span>
            </button>
        </div>
    </div>

    {{-- FILTER --}}
    <div class="px-2 mt-6">
        <form method="GET" action="{{ route('admin.dashboard') }}"
              x-data="dashboardFilter()"
              x-init="initFilter()"
              class="bg-white border rounded-2xl p-5">

            <div class="grid grid-cols-1 md:grid-cols-12 gap-4">

                {{-- Period --}}
                <div class="md:col-span-6"
                     x-data="dropdownController('period', '{{ $filterOptions['period'] }}', @js($filterOptions['periods']), 'Pilih Periode')"
                     x-init="initDropdown()"
                     @click.outside="closeDropdown()"
                     class="relative">

                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Filter Periode
                    </label>

                    <button type="button"
                            @click.prevent.stop="toggleDropdown()"
                            :class="isOpen ? 'border-[#10AF13] ring-1 ring-[#10AF13]' : 'border-gray-300'"
                            class="w-full h-[42px] px-3 py-2 rounded-lg border cursor-pointer flex justify-between items-center text-sm bg-white transition">
                        <span x-text="currentLabel" class="truncate"></span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                            stroke="#374151" stroke-width="2" :class="isOpen ? 'rotate-180' : ''"
                            class="flex-shrink-0 ml-2 transition-transform">
                            <path d="M6 9l6 6l6 -6" />
                        </svg>
                    </button>

                    <div x-show="isOpen" x-cloak x-transition
                         class="absolute z-20 mt-2 w-full bg-white border rounded-lg shadow-md max-h-60 overflow-y-auto">
                        <template x-for="option in options" :key="option.value">
                            <div @click.stop="selectOption(option.value, option.label)"
                                class="px-3 py-2 text-sm cursor-pointer hover:bg-gray-100"
                                :class="currentValue === option.value ? 'bg-[#10AF13]/5 text-[#10AF13] font-medium' : ''">
                                <span x-text="option.label"></span>
                            </div>
                        </template>
                    </div>

                    <input type="hidden" name="period" :value="currentValue">
                </div>

                {{-- Year (conditional) --}}
                <div class="md:col-span-3"
                     x-show="['specific_year', 'specific_month'].includes($root.querySelector('[name=period]').value)"
                     x-cloak
                     x-data="dropdownController('year', '{{ $filterOptions['year'] }}', @js($filterOptions['years']), 'Pilih Tahun')"
                     x-init="initDropdown()"
                     @click.outside="closeDropdown()"
                     class="relative">

                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tahun</label>

                    <button type="button"
                            @click.prevent.stop="toggleDropdown()"
                            :class="isOpen ? 'border-[#10AF13] ring-1 ring-[#10AF13]' : 'border-gray-300'"
                            class="w-full h-[42px] px-3 py-2 rounded-lg border cursor-pointer flex justify-between items-center text-sm bg-white transition">
                        <span x-text="currentLabel" class="truncate"></span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                            stroke="#374151" stroke-width="2" :class="isOpen ? 'rotate-180' : ''"
                            class="flex-shrink-0 ml-2 transition-transform">
                            <path d="M6 9l6 6l6 -6" />
                        </svg>
                    </button>

                    <div x-show="isOpen" x-cloak x-transition
                         class="absolute z-20 mt-2 w-full bg-white border rounded-lg shadow-md">
                        <template x-for="option in options" :key="option.value">
                            <div @click.stop="selectOption(option.value, option.label)"
                                class="px-3 py-2 text-sm cursor-pointer hover:bg-gray-100"
                                :class="currentValue === option.value ? 'bg-[#10AF13]/5 text-[#10AF13] font-medium' : ''">
                                <span x-text="option.label"></span>
                            </div>
                        </template>
                    </div>

                    <input type="hidden" name="year" :value="currentValue">
                </div>

                {{-- Month (conditional) --}}
                <div class="md:col-span-3"
                     x-show="$root.querySelector('[name=period]').value === 'specific_month'"
                     x-cloak
                     x-data="dropdownController('month', '{{ $filterOptions['month'] }}', @js($filterOptions['months']), 'Pilih Bulan')"
                     x-init="initDropdown()"
                     @click.outside="closeDropdown()"
                     class="relative">

                    <label class="block text-sm font-semibold text-gray-700 mb-2">Bulan</label>

                    <button type="button"
                            @click.prevent.stop="toggleDropdown()"
                            :class="isOpen ? 'border-[#10AF13] ring-1 ring-[#10AF13]' : 'border-gray-300'"
                            class="w-full h-[42px] px-3 py-2 rounded-lg border cursor-pointer flex justify-between items-center text-sm bg-white transition">
                        <span x-text="currentLabel" class="truncate"></span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                            stroke="#374151" stroke-width="2" :class="isOpen ? 'rotate-180' : ''"
                            class="flex-shrink-0 ml-2 transition-transform">
                            <path d="M6 9l6 6l6 -6" />
                        </svg>
                    </button>

                    <div x-show="isOpen" x-cloak x-transition
                         class="absolute z-20 mt-2 w-full bg-white border rounded-lg shadow-md max-h-60 overflow-y-auto">
                        <template x-for="option in options" :key="option.value">
                            <div @click.stop="selectOption(option.value, option.label)"
                                class="px-3 py-2 text-sm cursor-pointer hover:bg-gray-100"
                                :class="currentValue === option.value ? 'bg-[#10AF13]/5 text-[#10AF13] font-medium' : ''">
                                <span x-text="option.label"></span>
                            </div>
                        </template>
                    </div>

                    <input type="hidden" name="month" :value="currentValue">
                </div>

                {{-- Apply --}}
                <div :class="['specific_year', 'specific_month'].includes($root.querySelector('[name=period]').value) ?
                              ($root.querySelector('[name=period]').value === 'specific_month' ? 'md:col-span-12 mt-2' : 'md:col-span-6') :
                              'md:col-span-6'"
                     class="flex items-end">
                    <button type="submit"
                            class="w-full h-[42px] px-6 bg-[#10AF13] text-white rounded-lg hover:bg-[#0e8e0f] transition font-medium shadow-sm">
                        <span class="flex items-center justify-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M5 12l5 5l10 -10" />
                            </svg>
                            Terapkan Filter
                        </span>
                    </button>
                </div>
            </div>

            {{-- Quick Chips --}}
            <div class="flex flex-wrap gap-2 mt-4 pt-4 border-t">
                <span class="text-sm text-gray-600 font-medium">Quick Filter:</span>
                <a href="{{ route('admin.dashboard', ['period' => 'today']) }}"
                   class="px-3 py-1 text-xs rounded-full {{ request('period') === 'today' ? 'bg-[#10AF13] text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }} transition">
                    Hari Ini
                </a>
                <a href="{{ route('admin.dashboard', ['period' => 'this_week']) }}"
                   class="px-3 py-1 text-xs rounded-full {{ request('period') === 'this_week' ? 'bg-[#10AF13] text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }} transition">
                    Minggu Ini
                </a>
                <a href="{{ route('admin.dashboard', ['period' => 'this_month']) }}"
                   class="px-3 py-1 text-xs rounded-full {{ request('period') === 'this_month' || !request('period') ? 'bg-[#10AF13] text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }} transition">
                    Bulan Ini
                </a>
                <a href="{{ route('admin.dashboard', ['period' => 'this_year']) }}"
                   class="px-3 py-1 text-xs rounded-full {{ request('period') === 'this_year' ? 'bg-[#10AF13] text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }} transition">
                    Tahun Ini
                </a>
                <a href="{{ route('admin.dashboard', ['period' => 'all_time']) }}"
                   class="px-3 py-1 text-xs rounded-full {{ request('period') === 'all_time' ? 'bg-[#10AF13] text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }} transition">
                    Semua Waktu
                </a>
            </div>
        </form>
    </div>

    {{-- SECTION: STATS --}}
    <div class="px-2 mt-8">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-lg font-semibold">Ringkasan</h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @include('dashboard.card', [
                'title'=>'Total Batch',
                'value'=>$totalBatches,
                'icon'=>'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                    <path fill="none" stroke="#5EABD6" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2zm20 0h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>',
                'color'=>'text-[#5EABD6]'
            ])
            @include('dashboard.card', [
                'title'=>'Batch Aktif',
                'value'=>$activeBatches,
                'icon'=>'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                    <path fill="none" stroke="#10AF13" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2zm20 0h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7zM6 8h2m-2 4h2m8-4h2m-2 4h2"/></svg>',
                'color'=>'text-[#10AF13]'
            ])
            @include('dashboard.card', [
                'title'=>'Total Peserta',
                'value'=>$totalParticipants,
                'icon'=>'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2"><path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" /><path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                    <path d="M16 3.13a4 4 0 0 1 0 7.75" /><path d="M21 21v-2a4 4 0 0 0 -3 -3.85" /></svg>',
                'color'=>'text-[#AE00FF]'
            ])
            @include('dashboard.card', [
                'title'=>'Lulus',
                'value'=>$passedParticipants,
                'icon'=>'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2"><path d="M3 17l6 -6l4 4l8 -8" /><path d="M14 7l7 0l0 7" /></svg>',
                'color'=>'text-[#FF4D00]'
            ])
            @include('dashboard.card', [
                'title'=>'Cabang Aktif',
                'value'=>$activeBranches,
                'icon'=>'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2"><path d="M12 18.5l-3 -1.5l-6 3v-13l6 -3l6 3l6 -3v7.5" /><path d="M9 4v13" /><path d="M15 7v5.5" />
                    <path d="M21.121 20.121a3 3 0 1 0 -4.242 0c.418 .419 1.125 1.045 2.121 1.879c1.051 -.89 1.759 -1.516 2.121 -1.879z" />
                    <path d="M19 18v.01" /></svg>',
                'color'=>'text-[#64E2B7]'
            ])
            @include('dashboard.card', [
                'title'=>'Sertifikat',
                'value'=>$totalCertificates,
                'icon'=>'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2"><path d="M12 9m-6 0a6 6 0 1 0 12 0a6 6 0 1 0 -12 0" /><path d="M12 15l3.4 5.89l1.598 -3.233l3.598 .232l-3.4 -5.889" />
                    <path d="M6.802 12l-3.4 5.89l3.598 -.233l1.598 3.232l3.4 -5.889" /></svg>',
                'color'=>'text-[#D4AF37]'
            ])
        </div>
    </div>

    {{-- SECTION: CHARTS (lebih rapi) --}}
    <div class="px-2 mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Trend --}}
        <div class="bg-white border rounded-2xl p-6 flex flex-col">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold">Tren Bulanan</h2>
            </div>
            <div class="flex-1 min-h-[320px]">
                <canvas id="trendChart"></canvas>
            </div>
        </div>

        {{-- Status --}}
        <div class="bg-white border rounded-2xl p-6 flex flex-col">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold">Status Batch</h2>
            </div>
            <div class="flex-1 flex items-center justify-center min-h-[320px]">
                <canvas id="statusChart"></canvas>
            </div>
        </div>

        {{-- Kategori (full width supaya label panjang enak) --}}
        <div class="bg-white border rounded-2xl p-6 lg:col-span-2">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold">Distribusi Peserta per Kategori/Course</h2>
            </div>
            <div class="h-[380px]">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>
    </div>

    {{-- SECTION: RECENT BATCHES --}}
    <div class="px-2 mt-8">
        <div class="bg-white border rounded-2xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold">Batch Terbaru</h2>
            </div>

            <div class="space-y-4 max-h-[440px] overflow-y-auto pr-1">
                @forelse($recentBatches as $batch)
                    <div class="flex items-center justify-between p-4 border rounded-xl hover:bg-gray-50 transition">
                        <div class="flex-1">
                            <h3 class="text-md font-semibold text-gray-800">{{ $batch->title }}</h3>
                            <div class="flex flex-wrap items-center gap-2 mt-1">
                                <p class="text-sm font-medium text-[#737373]">
                                    <span class="inline-flex items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                                            <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                                        </svg>
                                        {{ $batch->trainer?->name ?? 'Belum ada trainer' }}
                                    </span>
                                </p>
                                <span class="text-[#D1D5DB]">•</span>
                                <p class="text-sm font-medium text-[#737373]">
                                    <span class="inline-flex items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M4 4h6v6h-6z" />
                                            <path d="M14 4h6v6h-6z" />
                                            <path d="M4 14h6v6h-6z" />
                                            <path d="M17 17m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />
                                        </svg>
                                        {{ $batch->category?->name ?? '-' }}
                                    </span>
                                </p>
                                <span class="text-[#D1D5DB]">•</span>
                                <p class="text-sm font-medium text-[#737373]">
                                    <span class="inline-flex items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" />
                                            <path d="M16 3v4" />
                                            <path d="M8 3v4" />
                                            <path d="M4 11h16" />
                                        </svg>
                                        {{ $batch->start_date->locale('id')->translatedFormat('d F Y') }}
                                    </span>
                                </p>
                                <span class="text-[#D1D5DB]">•</span>
                                <p class="text-sm font-medium text-[#737373]">
                                    <span class="inline-flex items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                                            <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                                            <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                            <path d="M21 21v-2a4 4 0 0 0 -3 -3.85" />
                                        </svg>
                                        {{ $batch->batch_participants_count }} peserta
                                    </span>
                                </p>
                            </div>
                        </div>
                        <span class="px-3 py-1 text-sm font-medium rounded-full whitespace-nowrap {{ badgeStatus($batch->status) }}">
                            {{ strtoupper($batch->status) }}
                        </span>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center py-12 text-gray-500">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="text-gray-400 mb-3">
                            <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                            <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                            <path d="M9 12l6 0" />
                            <path d="M9 16l6 0" />
                        </svg>
                        <p class="font-medium">Belum ada batch dalam periode ini</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // ============================================================
        // SYNC ALL (ADMIN)
        // ============================================================
        function syncAllAdmin() {
            return {
                loading: false,

                sync() {
                    if (this.loading) return;
                    this.loading = true;

                    fetch('{{ route('sync.all') }}', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                    })
                    .then(async (res) => {
                        const contentType = res.headers.get('content-type') || '';
                        if (!contentType.includes('application/json')) {
                            const text = await res.text();
                            throw new Error(text || 'Response bukan JSON');
                        }
                        const data = await res.json();
                        if (!res.ok) throw new Error(data?.message || 'Request gagal');
                        return data;
                    })
                    .then((data) => {
                        this.loading = false;
                        this.showNotification(!!data.success, data.message || 'Sync selesai');

                        if (data.success) {
                            setTimeout(() => {
                                window.location.href = window.location.href; // keep filter query
                            }, 1200);
                        }
                    })
                    .catch((err) => {
                        console.error('SYNC ADMIN ERROR:', err);
                        this.loading = false;
                        this.showNotification(false, err?.message || 'Terjadi kesalahan saat sync data.');
                    });
                },

                showNotification(success, message) {
                    const div = document.createElement('div');
                    div.innerHTML = `
                        <div x-data="{ show: true }"
                             x-show="show"
                             x-init="setTimeout(() => show = false, 4000)"
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 translate-y-4"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-200"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 translate-y-4"
                             class="fixed bottom-6 right-6 z-50 max-w-md">
                            <div class="flex items-center gap-3 ${success ? 'bg-[#10AF13]' : 'bg-red-600'} text-white px-5 py-4 rounded-xl shadow-2xl border border-white/20">
                                <span class="font-medium text-sm">${message}</span>
                            </div>
                        </div>
                    `;
                    document.body.appendChild(div.firstElementChild);
                    Alpine.initTree(document.body.lastElementChild);
                }
            }
        }

        function dashboardFilter() {
            return {
                initFilter() {
                    this.$nextTick(() => {
                        document.querySelectorAll('[x-data*="dropdownController"]').forEach(el => {
                            if (el.__x && el.__x.$data) {
                                el.__x.$data.isOpen = false;
                            }
                        });
                    });
                }
            }
        }

        function dropdownController(name, initialValue, options, placeholder) {
            const selectedOption = initialValue ? options.find(opt => String(opt.value) === String(initialValue)) : null;

            return {
                fieldName: name,
                options: options,
                currentValue: initialValue || '',
                currentLabel: selectedOption ? selectedOption.label : placeholder,
                isOpen: false,

                initDropdown() { this.isOpen = false; },
                toggleDropdown() { this.isOpen = !this.isOpen; },
                closeDropdown() { this.isOpen = false; },
                selectOption(value, label) {
                    this.currentValue = value;
                    this.currentLabel = label;
                    this.isOpen = false;
                }
            }
        }

        // Charts (tetap seperti punyamu)
        const trendCtx = document.getElementById('trendChart').getContext('2d');
        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: @json($monthlyTrend['labels']),
                datasets: [{
                    label: 'Batch',
                    data: @json($monthlyTrend['batches']),
                    borderColor: '#5EABD6',
                    backgroundColor: 'rgba(94, 171, 214, 0.1)',
                    tension: 0.4,
                    fill: true
                }, {
                    label: 'Peserta',
                    data: @json($monthlyTrend['participants']),
                    borderColor: '#AD49E1',
                    backgroundColor: 'rgba(173, 73, 225, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { padding: 15, usePointStyle: true } }
                },
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
            }
        });

        const statusCtx = document.getElementById('statusChart').getContext('2d');
        new Chart(statusCtx, {
            type: 'pie',
            data: {
                labels: ['Scheduled', 'Ongoing', 'Completed'],
                datasets: [{
                    data: [{{ $batchStatus['Scheduled'] }}, {{ $batchStatus['Ongoing'] }}, {{ $batchStatus['Completed'] }}],
                    backgroundColor: ['#3B82F6', '#10B981', '#F59E0B'],
                    borderColor: '#ffffff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'right', labels: { padding: 15, usePointStyle: true } } }
            }
        });

        const categoryCtx = document.getElementById('categoryChart').getContext('2d');
        new Chart(categoryCtx, {
            type: 'bar',
            data: {
                labels: @json($participantsPerCategory->pluck('name')),
                datasets: [{
                    label: 'Jumlah Peserta',
                    data: @json($participantsPerCategory->pluck('count')),
                    backgroundColor: '#AD49E1',
                    barPercentage: 0.6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: (context) => context.parsed.x + ' peserta' } }
                },
                scales: {
                    x: { beginAtZero: true, ticks: { stepSize: 1 } },
                    y: { ticks: { autoSkip: false, font: { size: 12 } } }
                }
            }
        });
    </script>
    @endpush
@endsection
