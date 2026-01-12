<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Training Next Level') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        [x-cloak] { 
            display: none !important; 
        }
        
        /* Prevent sidebar flash/glitch */
        @media (min-width: 1024px) {
            .sidebar-container {
                transform: translateX(0) !important;
            }
        }
        
        body {
            opacity: 1;
            transition: opacity 0.1s ease-in;
        }
        
        html {
            overflow-y: scroll;
        }
    </style>

    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-100">
    <div x-data="{ 
        openAddCategory: false,
        openEditCategory: false,
        openDeleteCategory: false,
        openAddBatch: false,
        openEditBatch: false,
        openDeleteBatch: false,
        openAddUser: false,
        openEditUser: false,
        openDeleteUser: false,
        deleteUserId: null,
        deleteUserName: '',
        deleteUserRole: '',
        selectedUser: {},
        selectedCategory: {},
        selectedBatch: {},
        tasks: [],
        addTask() {
            this.tasks.push({
                title: '',
                description: '',
                deadline: ''
            });
        },
        removeTask(index) {
            this.tasks.splice(index, 1);
        }
    }" class="min-h-screen">

        {{-- Sidebar akan di-include oleh layout turunan --}}
        @yield('sidebar')

        {{-- Main Content Area --}}
        <main class="lg:ml-64 min-h-screen p-8">
            @yield('content')
        </main>

        {{-- Success Notification --}}
        @if(session('success'))
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
                <div class="flex items-center gap-3 bg-[#10AF13] text-white px-5 py-4 rounded-xl shadow-2xl border border-white/20">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="flex-shrink-0">
                        <circle cx="12" cy="12" r="10" />
                        <path d="M5 12l5 5l10 -10" />
                    </svg>
                    <span class="font-medium text-sm">{{ session('success') }}</span>
                    <button @click="show = false" class="ml-2 hover:opacity-75 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" 
                            stroke="currentColor" stroke-width="2">
                            <path d="M18 6l-12 12M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        {{-- Error Notification --}}
        @if(session('error'))
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
                <div class="flex items-center gap-3 bg-red-600 text-white px-5 py-4 rounded-xl shadow-2xl border border-white/20">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="flex-shrink-0">
                        <circle cx="12" cy="12" r="10" />
                        <path d="M10 10l4 4m0 -4l-4 4" />
                    </svg>
                    <span class="font-medium text-sm">{{ session('error') }}</span>
                    <button @click="show = false" class="ml-2 hover:opacity-75 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" 
                            stroke="currentColor" stroke-width="2">
                            <path d="M18 6l-12 12M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        {{-- Warning Notification --}}
        @if(session('warning'))
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
                <div class="flex items-center gap-3 bg-yellow-500 text-white px-5 py-4 rounded-xl shadow-2xl border border-white/20">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="flex-shrink-0">
                        <circle cx="12" cy="12" r="10" />
                        <path d="M12 9v4M12 17h.01" />
                    </svg>
                    <span class="font-medium text-sm">{{ session('warning') }}</span>
                    <button @click="show = false" class="ml-2 hover:opacity-75 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" 
                            stroke="currentColor" stroke-width="2">
                            <path d="M18 6l-12 12M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        {{-- Validation Errors Notification --}}
        @if($errors->any())
            <div x-data="{ show: true }" 
                 x-show="show" 
                 x-init="setTimeout(() => show = false, 5000)"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 translate-y-4"
                 class="fixed bottom-6 right-6 z-50 max-w-md">
                <div class="flex items-start gap-3 bg-red-600 text-white px-5 py-4 rounded-xl shadow-2xl border border-white/20">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="flex-shrink-0 mt-0.5">
                        <circle cx="12" cy="12" r="10" />
                        <path d="M10 10l4 4m0 -4l-4 4" />
                    </svg>
                    <div class="flex-1">
                        @foreach($errors->all() as $error)
                            <p class="font-medium text-sm">{{ $error }}</p>
                            @if(!$loop->last)<div class="my-1"></div>@endif
                        @endforeach
                    </div>
                    <button @click="show = false" class="hover:opacity-75 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" 
                            stroke="currentColor" stroke-width="2">
                            <path d="M18 6l-12 12M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        @endif

    </div>

    {{-- Scripts dari child views --}}
    @stack('scripts')
</body>
</html>