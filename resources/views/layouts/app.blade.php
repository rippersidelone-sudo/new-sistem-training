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
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        [x-cloak] { display: none !important; }
        
        /* Prevent sidebar flash/glitch */
        @media (min-width: 1024px) {
            .sidebar-container {
                transform: translateX(0) !important;
            }
            .main-content {
                margin-left: 16rem;
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
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="flex min-h-screen" 
        x-data="{ 
            openAddUser: false,
            openEditUser: false,
            selectedUser: {},
            openDeleteUser: false,
            deleteUserId: null,
            deleteUserName: '',
            deleteUserRole: ''
        }">
        
        <x-sidebar />

        <main class="main-content flex-1 p-6 min-h-screen">
            @yield('content')
        </main>
    </div>

    {{-- Notifications --}}
    @if (session('success'))
        <div x-data="{ show: true }" 
             x-show="show" 
             x-init="setTimeout(() => show = false, 3000)"
             x-transition
             class="fixed bottom-6 right-6 z-50">
            <div class="flex items-center gap-3 bg-[#10AF13] text-white px-4 py-3 rounded-lg shadow-lg max-w-md">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" 
                    stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="9" />
                    <path d="M9 12l2 2l4 -4" />
                </svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div x-data="{ show: true }" 
             x-show="show" 
             x-init="setTimeout(() => show = false, 3000)"
             x-transition
             class="fixed bottom-6 right-6 z-50">
            <div class="flex items-center gap-3 bg-red-500 text-white px-4 py-3 rounded-lg shadow-lg max-w-md">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" 
                    stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="9" />
                    <path d="M10 10l4 4m0 -4l-4 4" />
                </svg>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div x-data="{ show: true }" 
             x-show="show" 
             x-init="setTimeout(() => show = false, 4000)"
             x-transition
             class="fixed bottom-6 right-6 z-50">
            <div class="flex items-center gap-3 bg-red-500 text-white px-4 py-3 rounded-lg shadow-lg max-w-md">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" 
                    stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="9" />
                    <path d="M10 10l4 4m0 -4l-4 4" />
                </svg>
                <span class="font-medium">{{ $errors->first() }}</span>
            </div>
        </div>
    @endif

    {{-- PENTING: Tempat untuk scripts dari child views --}}
    @stack('scripts')
</body>
</html>