{{-- resources/views/layouts/trainer.blade.php --}}
@extends('layouts.app')

@push('styles')
<style>
    [x-cloak] { display: none !important; }

    @media (min-width: 1024px) {
        .sidebar-container { transform: translateX(0) !important; }
    }

    @media (max-width: 1023px) {
        .sidebar-container:not(.mobile-open) { transform: translateX(-100%) !important; }
    }

    .sidebar-container {
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
</style>
@endpush

@section('sidebar')
<div x-data="sidebarController()" x-init="init()" @keydown.escape.window="closeSidebar()">

    {{-- Mobile Overlay --}}
    <div x-show="open"
         x-cloak
         @click="closeSidebar()"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/50 z-40 lg:hidden">
    </div>

    {{-- Sidebar --}}
    <div :class="{ 'mobile-open': open }"
         class="sidebar-container w-64 h-screen bg-[#10AF13] fixed top-0 left-0 flex flex-col justify-between p-4 z-50 lg:translate-x-0">

        <div>
            {{-- Avatar → Settings --}}
            <div class="flex items-center space-x-3 mt-4">
                <a href="{{ route('settings') }}"
                   class="w-12 h-12 bg-white rounded-full flex items-center justify-center font-bold text-xl shadow-lg hover:ring-2 hover:ring-white/60 transition shrink-0">
                    {{ strtoupper(substr(Auth::user()->name ?? '', 0, 1)) }}
                </a>

                <div class="flex-1 min-w-0">
                    <a href="{{ route('settings') }}" class="hover:underline underline-offset-2">
                        <h1 class="text-xl font-bold whitespace-normal text-black">
                            {{ Auth::user()->name }}
                        </h1>
                    </a>
                    <p class="text-sm text-[#E1EFE2] leading-tight whitespace-normal">
                        {{ Auth::user()->role->description ?? 'Trainer' }}
                    </p>
                </div>

                <button @click="closeSidebar()" class="lg:hidden text-black hover:bg-white/20 rounded-lg p-1 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 6l-12 12M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <hr class="border-[#E1EFE2]/40 mt-10 -mx-4">

            <nav class="mt-8 space-y-2">

                {{-- Dashboard --}}
                <a href="{{ route('trainer.dashboard') }}"
                   @click="handleNavigation()"
                   class="flex items-center space-x-3 p-3 rounded-lg font-medium transition-colors text-white
                   {{ request()->routeIs('trainer.dashboard') ? 'bg-[#E1EFE2] !text-black' : 'hover:bg-[#0e8e0f]' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M5 4h4a1 1 0 0 1 1 1v6a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1v-6a1 1 0 0 1 1 -1" />
                        <path d="M5 16h4a1 1 0 0 1 1 1v2a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1v-2a1 1 0 0 1 1 -1" />
                        <path d="M15 12h4a1 1 0 0 1 1 1v6a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1v-6a1 1 0 0 1 1 -1" />
                        <path d="M15 4h4a1 1 0 0 1 1 1v2a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1v-2a1 1 0 0 1 1 -1" />
                    </svg>
                    <span>Dashboard</span>
                </a>

                {{-- My Batches --}}
                <a href="{{ route('trainer.batches') }}"
                   @click="handleNavigation()"
                   class="flex items-center space-x-3 p-3 rounded-lg font-medium transition-colors text-white
                   {{ request()->routeIs('trainer.batches*') ? 'bg-[#E1EFE2] !text-black' : 'hover:bg-[#0e8e0f]' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0 -3 -3h-7z"/>
                        <path d="M22 3h-6a4 4 0 0 0 -4 4v14a3 3 0 0 1 3 -3h7z"/>
                    </svg>
                    <span>My Batches</span>
                </a>

                {{-- Approval Kehadiran --}}
                <a href="{{ route('trainer.approval-kehadiran') }}"
                   @click="handleNavigation()"
                   class="flex items-center space-x-3 p-3 rounded-lg font-medium transition-colors text-white
                   {{ request()->routeIs('trainer.approval-kehadiran') || request()->routeIs('trainer.attendance.*') ? 'bg-[#E1EFE2] !text-black' : 'hover:bg-[#0e8e0f]' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" />
                        <path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z" />
                        <path d="M9 14l2 2l4 -4" />
                    </svg>
                    <span>Approval Kehadiran</span>
                </a>

                {{-- Kelola Tugas --}}
                <a href="{{ route('trainer.kelola-tugas') }}"
                   @click="handleNavigation()"
                   class="flex items-center space-x-3 p-3 rounded-lg font-medium transition-colors text-white
                   {{ request()->routeIs('trainer.kelola-tugas') || request()->routeIs('trainer.tasks.*') ? 'bg-[#E1EFE2] !text-black' : 'hover:bg-[#0e8e0f]' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" />
                        <path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z" />
                        <path d="M9 12l.01 0" />
                        <path d="M13 12l2 0" />
                        <path d="M9 16l.01 0" />
                        <path d="M13 16l2 0" />
                    </svg>
                    <span>Kelola Tugas</span>
                </a>

                {{-- Penilaian Tugas --}}
                <a href="{{ route('trainer.penilaian-tugas') }}"
                   @click="handleNavigation()"
                   class="flex items-center space-x-3 p-3 rounded-lg font-medium transition-colors text-white
                   {{ request()->routeIs('trainer.penilaian-tugas') || request()->routeIs('trainer.submissions.*') ? 'bg-[#E1EFE2] !text-black' : 'hover:bg-[#0e8e0f]' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                        <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                        <path d="M9 15l2 2l4 -4" />
                    </svg>
                    <span>Penilaian Tugas</span>
                </a>

                {{-- Upload Materi --}}
                <a href="{{ route('trainer.upload-materi') }}"
                   @click="handleNavigation()"
                   class="flex items-center space-x-3 p-3 rounded-lg font-medium transition-colors text-white
                   {{ request()->routeIs('trainer.upload-materi') || request()->routeIs('trainer.materials.*') ? 'bg-[#E1EFE2] !text-black' : 'hover:bg-[#0e8e0f]' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
                        <path d="M7 9l5 -5l5 5" />
                        <path d="M12 4l0 12" />
                    </svg>
                    <span>Upload Materi</span>
                </a>

            </nav>
        </div>

        {{-- Logout --}}
        <div>
            <hr class="border-[#E1EFE2]/40 mb-4 -mx-4">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-full flex items-center justify-center space-x-3 bg-white py-2 rounded-lg text-black font-medium hover:bg-gray-100 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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
    <button @click="openSidebar()"
            class="fixed bottom-6 right-6 z-30 lg:hidden w-14 h-14 bg-[#10AF13] text-white rounded-full shadow-lg flex items-center justify-center hover:bg-[#0e8e0f] transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
             fill="none" stroke="currentColor" stroke-width="2">
            <line x1="3" y1="12" x2="21" y2="12" />
            <line x1="3" y1="6" x2="21" y2="6" />
            <line x1="3" y1="18" x2="21" y2="18" />
        </svg>
    </button>
</div>

@push('scripts')
<script>
function sidebarController() {
    return {
        open: false,
        init() {
            this.open = false;
            this.$nextTick(() => { this.open = false; });
        },
        openSidebar() {
            this.open = true;
            document.body.style.overflow = 'hidden';
        },
        closeSidebar() {
            this.open = false;
            document.body.style.overflow = '';
        },
        handleNavigation() {
            if (window.innerWidth < 1024) this.closeSidebar();
        }
    }
}
</script>
@endpush

@endsection