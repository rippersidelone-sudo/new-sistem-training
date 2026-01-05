@extends('layouts.trainer')

@section('content')
    <div class="px-2">
        <h1 class="text-2xl font-semibold">Dashboard Trainer</h1>
        <p class="text-[#737373] mt-2 font-medium">Selamat datang, {{ Auth::user()->name }}</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-8 px-2">
        @include('dashboard.card', [
            'title'=>'Batch',
            'value'=>3,
            'icon'=>'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                // <path fill="none" stroke="#5EABD6" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2zm20 0h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>',
            'color'=>'text-[#5EABD6]'
        ])
        @include('dashboard.card', [
            'title'=>'Total Peserta',
            'value'=>3,
            'icon'=>'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                class="icon icon-tabler icons-tabler-outline icon-tabler-users mb-8"><path stroke="none" d="M0 0h24v24H0z" 
                fill="none"/><path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" /><path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                <path d="M16 3.13a4 4 0 0 1 0 7.75" /><path d="M21 21v-2a4 4 0 0 0 -3 -3.85" /></svg>',
            'color'=>'text-[#AE00FF]'
        ])
        @include('dashboard.card', [
            'title'=>'Pending Grading',
            'value'=>1,
            'icon'=>'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-clock-hour-4">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 12l3 2" /><path d="M12 7v5" /></svg>',
            'color'=>'text-[#FFE100]'
        ])
        @include('dashboard.card', [
            'title'=>'Materials',
            'value'=>4,
            'icon'=>'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                class="icon icon-tabler icons-tabler-outline icon-tabler-upload"><path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" /><path d="M7 9l5 -5l5 5" /><path d="M12 4l0 12" /></svg>',
            'color'=>'text-[#10AF13]'
        ])
    </div>

    <div class="bg-white border rounded-2xl p-6 mt-8 mx-2">
        <h2 class="text-lg font-semibold mb-5">
            Batch Saya
        </h2>

        <div class="space-y-4">
            <!-- ITEM 1 -->
            <div class="flex items-center justify-between p-4 border rounded-xl hover:bg-gray-50 transition">
                <div>
                    <h3 class="text-md font-semibold text-gray-800">
                        Python Coder Batch 3
                    </h3>
                    <p class="text-md font-medium text-[#737373] flex flex-wrap gap-2">
                        <span>TRN-2025-002</span>
                        <span>•</span>
                        <span>15/11/2025</span>
                        <span>•</span>
                        <span>0 peserta</span>
                    </p>
                </div>

                <span class="px-3 py-1 text-sm uppercase font-medium rounded-full bg-blue-100 text-[#0059FF]">
                    Scheduled
                </span>
            </div>

            <!-- ITEM 2 -->
            <div class="flex items-center justify-between p-4 border rounded-xl hover:bg-gray-50 transition">
                <div>
                    <h3 class="text-md font-semibold text-gray-800">
                        Python Game Developer Batch 1
                    </h3>
                    <p class="text-md font-medium text-[#737373] flex flex-wrap gap-2">
                        <span>TRN-2025-001</span>
                        <span>•</span>
                        <span>10/11/2025</span>
                        <span>•</span>
                        <span>2 peserta</span>
                    </p>
                </div>

                <span class="px-3 py-1 text-sm uppercase font-medium rounded-full bg-green-100 text-[#10AF13]">
                    Ongoing
                </span>
            </div>

            <!-- ITEM 3 -->
            <div class="flex items-center justify-between p-4 border rounded-xl hover:bg-gray-50 transition">
                <div>
                    <h3 class="text-md font-semibold text-gray-800">
                        Web Development Fundamentals
                    </h3>
                    <p class="text-md font-medium text-[#737373] flex flex-wrap gap-2">
                        <span>TRN-2025-003</span>
                        <span>•</span>
                        <span>20/10/2025</span>
                        <span>•</span>
                        <span>1 peserta</span>
                    </p>
                </div>

                <span class="px-3 py-1 text-sm uppercase font-medium rounded-full bg-orange-100 text-[#FF4D00]">
                    Completed
                </span>
            </div>
        </div>
    </div>
@endsection