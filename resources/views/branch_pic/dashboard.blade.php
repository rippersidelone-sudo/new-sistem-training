@extends('layouts.branch-pic')

@section('content')
    <div class="px-2">
        <h1 class="text-2xl font-semibold">Dashboard Branch</h1>
        <p class="text-[#737373] mt-2 font-medium">Cabang Jakarta Pusat</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-8 px-2">
        @include('dashboard.card', [
            'title'=>'Total Peserta',
            'value'=>4,
            'icon'=>'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                class="icon icon-tabler icons-tabler-outline icon-tabler-users mb-8"><path stroke="none" d="M0 0h24v24H0z" 
                fill="none"/><path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" /><path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                <path d="M16 3.13a4 4 0 0 1 0 7.75" /><path d="M21 21v-2a4 4 0 0 0 -3 -3.85" /></svg>',
            'color'=>'text-[#AE00FF]'
        ])
        @include('dashboard.card', [
            'title'=>'Ongoing',
            'value'=>1,
            'icon'=>'<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-loader-2" width="24" height="24" 
                viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-width="2"><path d="M12 3 a9 9 0 1 0 9 9" /></svg>',
            'color'=>'text-[#10AF13]'
        ])
        @include('dashboard.card', [
            'title'=>'Completed',
            'value'=>0,
            'icon'=>'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                class="icon icon-tabler icons-tabler-outline icon-tabler-progress-check"><path stroke="none" d="M0 0h24v24H0z" 
                fill="none"/><path d="M10 20.777a8.942 8.942 0 0 1 -2.48 -.969" /><path d="M14 3.223a9.003 9.003 0 0 1 0 17.554" />
                <path d="M4.579 17.093a8.961 8.961 0 0 1 -1.227 -2.592" /><path d="M3.124 10.5c.16 -.95 .468 -1.85 .9 -2.675l.169 -.305" />
                <path d="M6.907 4.579a8.954 8.954 0 0 1 3.093 -1.356" /><path d="M9 12l2 2l4 -4" /></svg>',
            'color'=>'text-[#FF4D00]'
        ])
        @include('dashboard.card', [
            'title'=>'Sertifikat',
            'value'=>0,
            'icon'=>'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                class="icon icon-tabler icons-tabler-outline icon-tabler-award mb-8"><path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M12 9m-6 0a6 6 0 1 0 12 0a6 6 0 1 0 -12 0" /><path d="M12 15l3.4 5.89l1.598 -3.233l3.598 .232l-3.4 -5.889" />
                <path d="M6.802 12l-3.4 5.89l3.598 -.233l1.598 3.232l3.4 -5.889" /></svg>',
            'color'=>'text-[#D4AF37]'
        ])
    </div>

    <div class="bg-white border rounded-2xl p-6 mt-8 mx-2">
        <h2 class="text-lg font-semibold mb-5">
            Peserta Terbaru
        </h2>
        <div class="space-y-4 max-h-[455px] overflow-y-auto pr-1">
            <div class="flex items-center justify-between p-4 border rounded-xl hover:bg-gray-50 transition">
                <div>
                    <h3 class="text-md font-semibold text-gray-800">
                        Guru Peserta
                    </h3>
                    <p class="text-md font-medium text-[#737373] flex flex-wrap gap-2">
                        Python Game Developer Batch 1
                    </p>
                </div>
                <span class="px-3 py-1 text-sm uppercase font-medium rounded-full bg-green-100 text-[#10AF13]">
                    Ongoing
                </span>
            </div>
            <div class="flex items-center justify-between p-4 border rounded-xl hover:bg-gray-50 transition">
                <div>
                    <h3 class="text-md font-semibold text-gray-800">
                        Rina Wati
                    </h3>
                    <p class="text-md font-medium text-[#737373] flex flex-wrap gap-2">
                        Python Game Developer Batch 1
                    </p>
                </div>
                <span class="px-3 py-1 text-sm uppercase font-medium rounded-full bg-blue-100 text-[#0059FF]">
                    Approved
                </span>
            </div>
            <div class="flex items-center justify-between p-4 border rounded-xl hover:bg-gray-50 transition">
                <div>
                    <h3 class="text-md font-semibold text-gray-800">
                        Budi Hartono
                    </h3>
                    <p class="text-md font-medium text-[#737373] flex flex-wrap gap-2">
                        Python Coder Batch 3
                    </p>
                </div>
                <span class="px-3 py-1 text-sm uppercase font-medium rounded-full bg-gray-200 text-gray-700">
                    Registered
                </span>
            </div>
            <div class="flex items-center justify-between p-4 border rounded-xl hover:bg-gray-50 transition">
                <div>
                    <h3 class="text-md font-semibold text-gray-800">
                        Dewi Lestari
                    </h3>
                    <p class="text-md font-medium text-[#737373] flex flex-wrap gap-2">
                        Web Development Fundamentals
                    </p>
                </div>
                <span class="px-3 py-1 text-sm uppercase font-medium rounded-full bg-orange-100 text-[#FF4D00]">
                    Completed
                </span>
            </div>
            <div class="flex items-center justify-between p-4 border rounded-xl hover:bg-gray-50 transition">
                <div>
                    <h3 class="text-md font-semibold text-gray-800">
                        Dewi Lestari
                    </h3>
                    <p class="text-md font-medium text-[#737373] flex flex-wrap gap-2">
                        Web Development Fundamentals
                    </p>
                </div>
                <span class="px-3 py-1 text-sm uppercase font-medium rounded-full bg-orange-100 text-[#FF4D00]">
                    Completed
                </span>
            </div>
            <div class="flex items-center justify-between p-4 border rounded-xl hover:bg-gray-50 transition">
                <div>
                    <h3 class="text-md font-semibold text-gray-800">
                        Dewi Lestari
                    </h3>
                    <p class="text-md font-medium text-[#737373] flex flex-wrap gap-2">
                        Web Development Fundamentals
                    </p>
                </div>
                <span class="px-3 py-1 text-sm uppercase font-medium rounded-full bg-orange-100 text-[#FF4D00]">
                    Completed
                </span>
            </div>
        </div>
    </div>
@endsection