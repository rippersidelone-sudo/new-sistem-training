@extends('layouts.participant')

@section('content')
    <div class="px-2">
        <h1 class="text-2xl font-semibold">Sertifikat Peserta</h1>
        <p class="text-[#737373] mt-2 font-medium">Download sertifikat pelatihan yang telah Anda selesaikan</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-4 mt-8 px-2">
        @include('dashboard.card', [
            'title'=>'Total Sertifikat',
            'value'=>0,
            'icon'=>'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                class="icon icon-tabler icons-tabler-outline icon-tabler-award mb-8"><path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M12 9m-6 0a6 6 0 1 0 12 0a6 6 0 1 0 -12 0" /><path d="M12 15l3.4 5.89l1.598 -3.233l3.598 .232l-3.4 -5.889" />
                <path d="M6.802 12l-3.4 5.89l3.598 -.233l1.598 3.232l3.4 -5.889" /></svg>',
            'color'=>'text-[#D4AF37]'
        ])
        @include('dashboard.card', [
            'title'=>'Tahun Ini',
            'value'=>0,
            'icon'=>'<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-calendar" width="24" height="24" viewBox="0 0 24 24" 
                stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" />
                <path d="M16 3v4" /><path d="M8 3v4" /><path d="M4 11h16" /></svg>',
            'color'=>'text-[#0059FF]'
        ])
    </div>

    <!-- Tugas 1 -->
    <div class="bg-white border rounded-2xl p-6 mt-8 mx-2">
        <div class="flex justify-center mb-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 24 24" fill="none" 
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                class="icon icon-tabler icons-tabler-outline icon-tabler-award text-gray-300">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M12 9m-6 0a6 6 0 1 0 12 0a6 6 0 1 0 -12 0" />
                <path d="M12 15l3.4 5.89l1.598 -3.233l3.598 .232l-3.4 -5.889" />
                <path d="M6.802 12l-3.4 5.89l3.598 -.233l1.598 3.232l3.4 -5.889" />
            </svg>
        </div>
        <div class="flex justify-center mb-2">
            <p class="text-black text-md font-medium">Belum Ada Sertifikat</p>
        </div>
        <div class="flex justify-center">
            <p class="text-gray-700 text-md font-medium">Selesaikan pelatihan untuk mendapatkan sertifikat</p>
        </div>
    </div>
@endsection