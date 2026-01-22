@extends('layouts.participant')

@section('content')
    {{-- Notifications --}}
    @if(session('success'))
        <x-notification type="success">{{ session('success') }}</x-notification>
    @endif
    @if(session('error'))
        <x-notification type="error">{{ session('error') }}</x-notification>
    @endif

    {{-- Header --}}
    <div class="px-2">
        <h1 class="text-2xl font-semibold">Sertifikat Saya</h1>
        <p class="text-[#737373] mt-2 font-medium">Kumpulan sertifikat pelatihan yang telah Anda selesaikan</p>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-8 px-2">
        @include('dashboard.card', [
            'title' => 'Total Sertifikat',
            'value' => $totalCertificates,
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M12 9m-6 0a6 6 0 1 0 12 0a6 6 0 1 0 -12 0" />
                <path d="M12 15l3.4 5.89l1.598 -3.233l3.598 .232l-3.4 -5.889" />
                <path d="M6.802 12l-3.4 5.89l3.598 -.233l1.598 3.232l3.4 -5.889" /></svg>',
            'color' => 'text-[#D4AF37]'
        ])
        @include('dashboard.card', [
            'title' => 'Tahun Ini',
            'value' => $thisYearCount,
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M11.795 21h-6.795a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v4" />
                <path d="M18 18m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                <path d="M15 3v4" /><path d="M7 3v4" /><path d="M3 11h16" />
                <path d="M18 16.496v1.504l1 1" /></svg>',
            'color' => 'text-[#10AF13]'
        ])
    </div>

    {{-- Filter Bar --}}
    <div class="mt-8 px-2">
        <x-filter-bar 
            :action="route('participant.sertifikat')"
            :hideSearch="true"
            :filters="[
                [
                    'name' => 'year',
                    'placeholder' => 'Semua Tahun',
                    'options' => array_merge(
                        [['value' => '', 'label' => 'Semua Tahun']],
                        $availableYears->map(fn($y) => ['value' => $y, 'label' => 'Tahun ' . $y])->toArray()
                    )
                ]
            ]"
        />
    </div>

    {{-- Certificates List --}}
    <div class="grid gap-6 mt-8 px-2">
        @if($certificates->isEmpty())
            {{-- Empty State --}}
            <div class="bg-white border rounded-2xl p-12 text-center">
                <div class="flex justify-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" 
                        stroke="#d1d5db" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M12 9m-6 0a6 6 0 1 0 12 0a6 6 0 1 0 -12 0" />
                        <path d="M12 15l3.4 5.89l1.598 -3.233l3.598 .232l-3.4 -5.889" />
                        <path d="M6.802 12l-3.4 5.89l3.598 -.233l1.598 3.232l3.4 -5.889" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Belum Ada Sertifikat</h3>
                <p class="text-gray-600 mb-6">
                    Selesaikan pelatihan untuk mendapatkan sertifikat
                </p>
                <a href="{{ route('participant.pelatihan') }}" 
                   class="inline-flex items-center gap-2 px-6 py-3 bg-[#10AF13] text-white rounded-lg hover:bg-[#0e8e0f] transition font-semibold">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" 
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0 -3 -3h-7z"/>
                        <path d="M22 3h-6a4 4 0 0 0 -4 4v14a3 3 0 0 1 3 -3h7z"/>
                    </svg>
                    Lihat Pelatihan Saya
                </a>
            </div>
        @else
            {{-- Certificates Grid --}}
            @foreach($certificatesByYear as $year => $yearCertificates)
                <div class="bg-white border rounded-2xl p-6">
                    <h2 class="text-lg font-semibold mb-4 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" 
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M11.795 21h-6.795a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v4" />
                            <path d="M15 3v4" /><path d="M7 3v4" /><path d="M3 11h16" />
                        </svg>
                        Tahun {{ $year }}
                        <span class="text-sm font-normal text-gray-500">({{ $yearCertificates->count() }} sertifikat)</span>
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($yearCertificates as $cert)
                            <div class="border rounded-xl p-5 hover:shadow-md transition group">
                                {{-- Certificate Icon --}}
                                <div class="flex justify-center mb-4">
                                    <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" 
                                            stroke="#D4AF37" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M12 9m-6 0a6 6 0 1 0 12 0a6 6 0 1 0 -12 0" />
                                            <path d="M12 15l3.4 5.89l1.598 -3.233l3.598 .232l-3.4 -5.889" />
                                            <path d="M6.802 12l-3.4 5.89l3.598 -.233l1.598 3.232l3.4 -5.889" />
                                        </svg>
                                    </div>
                                </div>

                                {{-- Certificate Info --}}
                                <div class="text-center mb-4">
                                    <h3 class="font-semibold text-gray-800 mb-1 line-clamp-2">
                                        {{ $cert->batch->title }}
                                    </h3>
                                    <p class="text-sm text-gray-600 mb-1">
                                        {{ $cert->batch->category->name }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        Diterbitkan: {{ formatDate($cert->issued_at) }}
                                    </p>
                                </div>

                                {{-- Actions --}}
                                <div class="flex gap-2">
                                    @if($cert->file_path)
                                        <a href="{{ route('participant.sertifikat.download', $cert) }}" 
                                           class="flex-1 px-3 py-2 bg-[#10AF13] text-white rounded-lg text-sm font-medium hover:bg-[#0e8e0f] transition text-center flex items-center justify-center gap-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" 
                                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
                                                <path d="M7 11l5 5l5 -5" />
                                                <path d="M12 4l0 12" />
                                            </svg>
                                            Download
                                        </a>
                                    @else
                                        <div class="flex-1 px-3 py-2 bg-orange-100 text-[#FF4D00] rounded-lg text-sm font-medium text-center">
                                            Sedang Diproses
                                        </div>
                                    @endif
                                    
                                    <a href="{{ route('participant.sertifikat.show', $cert) }}" 
                                       class="px-3 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" 
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                            <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        @endif
    </div>
@endsection