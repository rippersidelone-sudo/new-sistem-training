{{-- resources/views/admin/audit-log.blade.php --}}
@extends('layouts.admin')

@section('content')
    <div class="px-2">
        <h1 class="text-2xl font-semibold">Audit Log</h1>
        <p class="text-[#737373] mt-2 font-medium">Riwayat aktivitas sistem dan perubahan data</p>
    </div>

    {{-- Filter Bar --}}
    <div class="mt-8 mx-2">
        <x-filter-bar
            :action="route('admin.audit.index')"
            searchPlaceholder="Cari user, email, atau deskripsi..."
            :filters="$filterOptions"
        />
    </div>

    {{-- Statistics Cards --}}
    <div class="mt-6 mx-2 grid grid-cols-2 lg:grid-cols-4 gap-4">
        @php $col = $activities->getCollection(); @endphp
        @include('dashboard.card', [
            'title' => 'Total Aktivitas',
            'value' => $activities->total(),
            'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2"/></svg>',
            'color' => 'text-[#0059FF]'
        ])
        @include('dashboard.card', [
            'title' => 'Create',
            'value' => $col->where('event','created')->count(),
            'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5l0 14"/><path d="M5 12l14 0"/></svg>',
            'color' => 'text-[#10AF13]'
        ])
        @include('dashboard.card', [
            'title' => 'Update',
            'value' => $col->where('event','updated')->count(),
            'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M7 7h-1a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2-2v-1"/><path d="M20.385 6.585a2.1 2.1 0 0 0-2.97-2.97l-8.415 8.385v3h3l8.385-8.415z"/></svg>',
            'color' => 'text-[#0059FF]'
        ])
        @include('dashboard.card', [
            'title' => 'Delete',
            'value' => $col->where('event','deleted')->count(),
            'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 7l16 0"/><path d="M10 11l0 6"/><path d="M14 11l0 6"/><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2l1-12"/><path d="M9 7v-3a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v3"/></svg>',
            'color' => 'text-red-600'
        ])
    </div>

    {{-- Tabel --}}
    <div class="bg-white border rounded-2xl mt-6 mx-2">
        <div class="px-6 py-4 border-b flex items-center justify-between flex-wrap gap-2">
            <div>
                <h2 class="text-lg font-semibold">Riwayat Aktivitas</h2>
                <p class="text-sm text-gray-500 mt-0.5">
                    Menampilkan
                    <span class="font-semibold text-gray-800">{{ $activities->firstItem() ?? 0 }}</span>–<span class="font-semibold text-gray-800">{{ $activities->lastItem() ?? 0 }}</span>
                    dari <span class="font-semibold text-gray-800">{{ $activities->total() }}</span> aktivitas
                    @if($activeFiltersCount > 0)
                        &nbsp;·&nbsp;<span class="text-[#10AF13] font-semibold">{{ $activeFiltersCount }} filter aktif</span>
                    @endif
                </p>
            </div>
            <div class="flex items-center gap-3 text-xs font-semibold text-gray-500">
                <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-green-500"></span>Create</span>
                <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-blue-500"></span>Update</span>
                <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-red-500"></span>Delete</span>
            </div>
        </div>

        @if($activities->isEmpty())
            <div class="text-center py-20 text-gray-500">
                <svg xmlns="http://www.w3.org/2000/svg" width="56" height="56" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="1.5" class="mx-auto mb-4 text-gray-300">
                    <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    <path d="M9 12h6M9 16h6"/>
                </svg>
                <p class="font-semibold text-lg">Tidak ada aktivitas ditemukan</p>
                @if($activeFiltersCount > 0)
                    <p class="text-sm mt-1 text-gray-400">Coba ubah filter atau reset pencarian</p>
                @endif
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-[#F1F1F1]">
                        <tr class="text-left text-sm font-semibold text-gray-700">
                            <th class="px-4 py-3 w-12 text-center">No</th>
                            <th class="px-4 py-3 w-24">Aksi</th>
                            <th class="px-4 py-3 w-32">Modul</th>
                            <th class="px-4 py-3">Data Target</th>
                            <th class="px-4 py-3">Oleh User</th>
                            <th class="px-4 py-3">Role / Cabang</th>
                            <th class="px-4 py-3 w-56">Perubahan</th>
                            <th class="px-4 py-3 w-36">Waktu Aktivitas</th>
                            <th class="px-4 py-3 w-36">Last Sync</th>
                            <th class="px-4 py-3 w-36">Terakhir Update</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @php $offset = ($activities->currentPage() - 1) * $activities->perPage(); @endphp
                        @foreach($activities as $log)
                        <tr class="hover:bg-gray-50/70 transition align-top">

                            <td class="px-4 py-4 text-center text-gray-600 font-medium">
                                {{ $offset + $loop->iteration }}
                            </td>

                            {{-- Aksi --}}
                            <td class="px-4 py-4 whitespace-nowrap">
                                @php
                                    $eventColor = match($log['event']) {
                                        'created' => 'bg-green-100 text-green-700 border border-green-200',
                                        'updated' => 'bg-blue-100 text-blue-700 border border-blue-200',
                                        'deleted' => 'bg-red-100 text-red-700 border border-red-200',
                                        default   => 'bg-gray-100 text-gray-600 border border-gray-200',
                                    };
                                    $eventDot = match($log['event']) {
                                        'created' => 'bg-green-500',
                                        'updated' => 'bg-blue-500',
                                        'deleted' => 'bg-red-500',
                                        default   => 'bg-gray-400',
                                    };
                                @endphp
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold uppercase {{ $eventColor }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $eventDot }}"></span>
                                    {{ $log['event'] }}
                                </span>
                            </td>

                            {{-- Modul --}}
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-gray-100 text-gray-700">
                                    {{ $log['subject_type'] }}
                                </span>
                            </td>

                            {{-- Data Target --}}
                            <td class="px-4 py-4">
                                <p class="font-medium text-gray-900 max-w-[160px] truncate" title="{{ $log['subject_label'] }}">
                                    {{ $log['subject_label'] }}
                                </p>
                                <p class="text-xs text-gray-400 mt-0.5 max-w-[160px] truncate" title="{{ $log['description'] }}">
                                    {{ $log['description'] }}
                                </p>
                            </td>

                            {{-- Oleh User --}}
                            <td class="px-4 py-4 whitespace-nowrap">
                                <p class="font-semibold text-gray-900">{{ $log['user_name'] }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">{{ $log['user_email'] }}</p>
                            </td>

                            {{-- Role / Cabang --}}
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span class="text-xs font-semibold px-2.5 py-1 rounded-lg {{ badgeRole($log['role']) }}">
                                    {{ $log['role'] }}
                                </span>
                                @if($log['user_branch'] !== '-')
                                    <p class="text-xs text-gray-400 mt-1">{{ $log['user_branch'] }}</p>
                                @endif
                            </td>

                            {{-- Perubahan --}}
                            <td class="px-4 py-4">
                                @if(count($log['changes']) > 0)
                                    <div class="space-y-1.5 max-h-24 overflow-y-auto pr-1">
                                        @foreach($log['changes'] as $change)
                                            <div class="text-xs leading-snug">
                                                <span class="font-semibold text-gray-600">{{ $change['field'] }}:</span>
                                                <span class="text-red-400 line-through ml-1">{{ \Str::limit($change['old'], 18) }}</span>
                                                <span class="text-gray-400 mx-0.5">→</span>
                                                <span class="text-green-600 font-medium">{{ \Str::limit($change['new'], 18) }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @elseif($log['event'] === 'created')
                                    <span class="text-xs text-green-600 italic">Data baru dibuat</span>
                                @elseif($log['event'] === 'deleted')
                                    <span class="text-xs text-red-500 italic">Data dihapus</span>
                                @else
                                    <span class="text-xs text-gray-300">—</span>
                                @endif
                            </td>

                            {{-- Waktu Aktivitas --}}
                            <td class="px-4 py-4 whitespace-nowrap">
                                <p class="text-xs text-gray-700 font-medium">{{ $log['created_at'] }}</p>
                            </td>

                            {{-- Last Sync --}}
                            <td class="px-4 py-4 whitespace-nowrap">
                                @if($log['last_synced_at'] !== '-')
                                    <div class="flex items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24"
                                            fill="none" stroke="#10AF13" stroke-width="2.5">
                                            <path d="M20 11a8.1 8.1 0 0 0-15.5-2m-.5-4v4h4"/>
                                            <path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4"/>
                                        </svg>
                                        <span class="text-xs text-gray-600">{{ $log['last_synced_at'] }}</span>
                                    </div>
                                @else
                                    <span class="text-xs text-gray-300">—</span>
                                @endif
                            </td>

                            {{-- Terakhir Update --}}
                            <td class="px-4 py-4 whitespace-nowrap">
                                <p class="text-xs text-gray-700 font-medium">{{ $log['updated_at'] }}</p>
                            </td>

                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="px-6 py-4 border-t">
                <x-pagination :paginator="$activities" />
            </div>
        @endif
    </div>

<style>[x-cloak] { display: none !important; }</style>
@endsection