{{-- resources/views/layouts/coordinator.blade.php --}}
@extends('layouts.app')

@section('sidebar')
<div x-data="{ open: false }" x-cloak>
    
    {{-- Mobile Overlay --}}
    <div x-show="open" 
         @click="open = false"
         x-transition
         class="fixed inset-0 bg-black/50 z-40 lg:hidden">
    </div>

    {{-- Sidebar --}}
    <div :class="open ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
         class="sidebar-container w-64 h-screen bg-[#10AF13] fixed top-0 left-0 flex flex-col justify-between p-4 z-50 transition-transform duration-300 lg:z-auto">
        
        {{-- USER HEADER --}}
        <div>
            <div class="flex items-center space-x-3 mt-4">
                {{-- Inisial --}}
                <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center font-bold text-xl shadow-lg">
                    {{ Auth::user()->initials ?? strtoupper(substr(Auth::user()->name ?? '', 0, 1)) }}
                </div>

                {{-- Nama & Role --}}
                <div class="flex-1 min-w-0">
                    <h1 class="text-xl font-bold whitespace-normal text-black">
                        {{ Auth::user()->name }}
                    </h1>
                    <p class="text-sm text-[#E1EFE2] leading-tight whitespace-normal">
                        {{ Auth::user()->role->description ?? 'Training Coordinator' }}
                    </p>
                </div>

                {{-- Close Mobile --}}
                <button @click="open = false" class="lg:hidden text-black">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 6l-12 12M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <hr class="border-[#E1EFE2]/40 mt-10 -mx-4">

            {{-- MENU LIST --}}
            <nav class="mt-8 space-y-2">
                {{-- Dashboard --}}
                <a href="{{ route('coordinator.dashboard') }}" 
                   class="flex items-center space-x-3 p-3 rounded-lg font-medium transition-colors text-white
                   {{ request()->routeIs('coordinator.dashboard') ? 'bg-[#E1EFE2] !text-black' : 'hover:bg-[#0e8e0f]' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M5 4h4a1 1 0 0 1 1 1v6a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1v-6a1 1 0 0 1 1 -1" />
                        <path d="M5 16h4a1 1 0 0 1 1 1v2a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1v-2a1 1 0 0 1 1 -1" />
                        <path d="M15 12h4a1 1 0 0 1 1 1v6a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1v-6a1 1 0 0 1 1 -1" />
                        <path d="M15 4h4a1 1 0 0 1 1 1v2a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1v-2a1 1 0 0 1 1 -1" />
                    </svg>
                    <span>Dashboard</span>
                </a>

                {{-- Kategori Pelatihan --}}
                <a href="{{ route('coordinator.categories.index') }}" 
                   class="flex items-center space-x-3 p-3 rounded-lg font-medium transition-colors text-white
                   {{ request()->routeIs('coordinator.categories.*') ? 'bg-[#E1EFE2] !text-black' : 'hover:bg-[#0e8e0f]' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M12 3l-8 4l8 4l8 -4l-8 -4" />
                        <path d="M4 12l8 4l8 -4" />
                        <path d="M4 16l8 4l8 -4" />
                    </svg>
                    <span>Kategori Pelatihan</span>
                </a>

                {{-- Batch Management --}}
                <a href="{{ route('coordinator.batches.index') }}" 
                   class="flex items-center space-x-3 p-3 rounded-lg font-medium transition-colors text-white
                   {{ request()->routeIs('coordinator.batches.*') ? 'bg-[#E1EFE2] !text-black' : 'hover:bg-[#0e8e0f]' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2zm20 0h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                    </svg>
                    <span>Batch Management</span>
                </a>

                {{-- Validasi Peserta --}}
                <a href="{{ route('coordinator.participants.index') }}" 
                   class="flex items-center space-x-3 p-3 rounded-lg font-medium transition-colors text-white
                   {{ request()->routeIs('coordinator.participants.*') ? 'bg-[#E1EFE2] !text-black' : 'hover:bg-[#0e8e0f]' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                        <path d="M6 21v-2a4 4 0 0 1 4 -4h4" />
                        <path d="M15 19l2 2l4 -4" />
                    </svg>
                    <span>Validasi Peserta</span>
                </a>

                {{-- Monitoring Absensi --}}
                <a href="{{ route('coordinator.monitoring.attendance') }}" 
                   class="flex items-center space-x-3 p-3 rounded-lg font-medium transition-colors text-white
                   {{ request()->routeIs('coordinator.monitoring.*') ? 'bg-[#E1EFE2] !text-black' : 'hover:bg-[#0e8e0f]' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" />
                        <path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z" />
                        <path d="M9 14l2 2l4 -4" />
                    </svg>
                    <span>Monitoring Absensi</span>
                </a>

                {{-- Laporan --}}
                <a href="{{ route('coordinator.reports.index') }}" 
                   class="flex items-center space-x-3 p-3 rounded-lg font-medium transition-colors text-white
                   {{ request()->routeIs('coordinator.reports.*') ? 'bg-[#E1EFE2] !text-black' : 'hover:bg-[#0e8e0f]' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" />
                        <path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z" />
                        <path d="M9 12h6" />
                        <path d="M9 16h6" />
                    </svg>
                    <span>Laporan</span>
                </a>

                {{-- Settings --}}
                <a href="{{ route('coordinator.settings') }}" 
                   class="flex items-center space-x-3 p-3 rounded-lg font-medium transition-colors text-white
                   {{ request()->routeIs('coordinator.settings') ? 'bg-[#E1EFE2] !text-black' : 'hover:bg-[#0e8e0f]' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z" />
                        <path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                    </svg>
                    <span>Settings</span>
                </a>
            </nav>
        </div>

        {{-- LOGOUT --}}
        <div>
            <hr class="border-[#E1EFE2]/40 mb-4 -mx-4">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                @method('POST')

                <button type="submit" class="w-full flex items-center justify-center space-x-3 bg-white py-2 rounded-lg text-black font-medium hover:bg-gray-100 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2" />
                        <path d="M9 12h12l-3 -3" />
                        <path d="M18 15l3 -3" />
                    </svg>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </div>

    {{-- Mobile Menu Button --}}
    <button @click="open = true" class="fixed bottom-6 right-6 z-30 lg:hidden w-14 h-14 bg-[#10AF13] text-white rounded-full shadow-lg flex items-center justify-center hover:bg-[#0e8e0f] transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="3" y1="12" x2="21" y2="12" />
            <line x1="3" y1="6" x2="21" y2="6" />
            <line x1="3" y1="18" x2="21" y2="18" />
        </svg>
    </button>
</div>
@endsection