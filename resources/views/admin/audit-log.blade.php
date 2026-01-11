@extends('layouts.app')

@section('content')
    <div class="px-2">
        <h1 class="text-2xl font-semibold">Audit Log</h1>
        <p class="text-[#737373] mt-2 font-medium">Riwayat aktivitas sistem dan perubahan data</p>
    </div>

    <!-- Filter Bar Component - GANTI FILTER LAMA! -->
    <div class="mt-8 px-2">
        <x-filter-bar
            :action="route('admin.audit.index')"
            method="GET"
            :showSearch="true"
            searchPlaceholder="Cari user atau detail aktivitas..."
            :filters="$filterOptions"
            :showDateRange="false"
            :showExport="false"
            :activeFiltersCount="$activeFiltersCount"
        />
    </div>

    <!-- Riwayat Aktivitas -->
    <div class="bg-white border rounded-2xl p-6 mt-8 mx-2">
        <div class="flex justify-between items-center mb-5">
            <div>
                <h2 class="text-lg font-semibold">Riwayat Aktivitas</h2>
                <p class="text-sm text-gray-500 mt-1">Total: {{ $auditLogs->count() }} aktivitas</p>
            </div>
        </div>

        @if($auditLogs->isEmpty())
        <div class="text-center py-12 text-gray-500">
            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="1.5" class="mx-auto mb-4 text-gray-400">
                <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                <path d="M9 12h6" />
                <path d="M9 16h6" />
            </svg>
            <p class="font-medium">Tidak ada aktivitas ditemukan</p>
            <p class="text-sm mt-1">Coba ubah filter atau kata kunci pencarian</p>
        </div>
        @else
        <div class="space-y-4 max-h-[70vh] overflow-y-auto pr-2">
            @foreach($auditLogs as $log)
            <div class="px-4 py-4 border rounded-xl hover:bg-gray-50 transition">
                <div class="flex items-center gap-3 flex-wrap">
                    <!-- Action Badge -->
                    <div class="rounded-lg bg-gray-200 px-3 py-1.5">
                        <p class="text-xs font-bold text-gray-800 uppercase">
                            {{ str_replace('_', ' ', $log['action']) }}
                        </p>
                    </div>
                    
                    <!-- Role Badge -->
                    <div class="rounded-lg border border-gray-300 px-3 py-1.5">
                        <p class="text-xs font-semibold {{ badgeRoleText($log['role']) }}">
                            {{ $log['role'] }}
                        </p>
                    </div>
                    
                    <!-- User Name -->
                    <div>
                        <p class="text-base font-semibold text-gray-900">
                            {{ $log['user'] }}
                        </p>
                    </div>
                    
                    <!-- Timestamp -->
                    <div class="ml-auto text-right">
                        <p class="text-sm font-medium text-gray-500">
                            {{ \Carbon\Carbon::parse($log['created_at'])->format('d M Y, H:i') }}
                        </p>
                    </div>
                </div>
                
                <!-- Description -->
                <div class="mt-3 pl-1">
                    <p class="text-sm text-gray-700 leading-relaxed">
                        {{ $log['description'] }}
                    </p>
                </div>
            </div>
            @endforeach
        </div>

        @if($auditLogs->count() > 0)
        <div class="mt-6 pt-4 border-t flex justify-between items-center text-sm">
            <p class="text-gray-600">
                Menampilkan {{ $auditLogs->count() }} aktivitas
            </p>
            @if($auditLogs->count() >= 50)
            <p class="text-[#10AF13] font-medium">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" 
                    stroke="currentColor" stroke-width="2" class="inline mr-1">
                    <circle cx="12" cy="12" r="10"/>
                    <path d="M12 16v-4"/>
                    <path d="M12 8h.01"/>
                </svg>
                Gunakan filter untuk mempersempit pencarian
            </p>
            @endif
        </div>
        @endif
        @endif
    </div>
@endsection