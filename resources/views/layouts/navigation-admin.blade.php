{{-- <!-- resources/views/layouts/navigation-admin.blade.php -->
<div class="w-64 h-screen bg-[#10AF13] fixed top-0 left-0 flex flex-col justify-between p-4 text-black">

    <!-- USER HEADER -->
    <div>
        <div class="flex items-center space-x-3 mt-4">
            <!-- Inisial -->
            <div class="w-12 h-12 rounded-full bg-white flex items-center justify-center text-xl font-bold">
                {{ Auth::user()->initials }}
            </div>

            <!-- Nama & Role -->
            <div>
                <h1 class="text-xl font-bold">
                    {{ Auth::user()->name }}
                </h1>
                <p class="text-sm text-[#E1EFE2] leading-tight">
                    {{ Auth::user()->role->description ?? 'HQ Admin' }}
                </p>
            </div>
        </div>

        <hr class="border-[#E1EFE2] mt-10 -mx-4">

        <!-- MENU LIST -->
        <nav class="mt-8 space-y-2">
            <!-- Dashboard -->
            <a href="{{ route('admin.dashboard') }}"
               class="flex items-center space-x-3 p-3 rounded-lg font-medium transition-colors
               {{ request()->routeIs('admin.dashboard') ? 'bg-[#E1EFE2]' : 'hover:bg-[#0e8e0f]' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M5 4h4a1 1 0 0 1 1 1v6a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1v-6a1 1 0 0 1 1 -1" />
                    <path d="M5 16h4a1 1 0 0 1 1 1v2a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1v-2a1 1 0 0 1 1 -1" />
                    <path d="M15 12h4a1 1 0 0 1 1 1v6a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1v-6a1 1 0 0 1 1 -1" />
                    <path d="M15 4h4a1 1 0 0 1 1 1v2a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1v-2a1 1 0 0 1 1 -1" />
                </svg>
                <span>Master Dashboard</span>
            </a>

            <!-- Batch Oversight -->
            <a href="{{ route('admin.batch-oversight.index') }}"
               class="flex items-center space-x-3 p-3 rounded-lg font-medium transition-colors
               {{ request()->routeIs('admin.batch-oversight.*') ? 'bg-[#E1EFE2]' : 'hover:bg-[#0e8e0f]' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2zm20 0h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                </svg>
                <span>Batch Oversight</span>
            </a>

            <!-- Role & Permission -->
            <a href="{{ route('admin.users.index') }}"  <!-- atau route('admin.roles.index') jika pakai resource roles -->
               class="flex items-center space-x-3 p-3 rounded-lg font-medium transition-colors
               {{ request()->routeIs(['admin.users.*', 'admin.roles.*']) ? 'bg-[#E1EFE2]' : 'hover:bg-[#0e8e0f]' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M12 3a12 12 0 0 0 8.5 3a12 12 0 0 1 -8.5 15a12 12 0 0 1 -8.5 -15a12 12 0 0 0 8.5 -3" />
                </svg>
                <span>Role & Permission</span>
            </a>

            <!-- Global Report -->
            <a href="{{ route('admin.reports.index') }}"  <!-- sesuaikan dengan nama route yang benar -->
               class="flex items-center space-x-3 p-3 rounded-lg font-medium transition-colors
               {{ request()->routeIs('admin.reports.*') ? 'bg-[#E1EFE2]' : 'hover:bg-[#0e8e0f]' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" />
                    <path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z" />
                    <path d="M9 12h6" />
                    <path d="M9 16h6" />
                </svg>
                <span>Global Report</span>
            </a>

            <!-- Audit Log -->
            <a href="{{ route('admin.audit.index') }}"
               class="flex items-center space-x-3 p-3 rounded-lg font-medium transition-colors
               {{ request()->routeIs('admin.audit.*') ? 'bg-[#E1EFE2]' : 'hover:bg-[#0e8e0f]' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M8 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h5.697" />
                    <path d="M18 12v-5a2 2 0 0 0 -2 -2h-2" />
                    <path d="M8 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z" />
                    <path d="M8 11h4" />
                    <path d="M8 15h3" />
                    <path d="M16.5 17.5m-2.5 0a2.5 2.5 0 1 0 5 0a2.5 2.5 0 1 0 -5 0" />
                    <path d="M18.5 19.5l2.5 2.5" />
                </svg>
                <span>Audit Log</span>
            </a>

            <!-- ITEM 6 -->
            <a href="{{ route('settings') }}" class="flex items-center space-x-3 p-3 text-black  rounded-lg 
                {{ request()->routeIs('settings') ? 'bg-[#E1EFE2]' : 'hover:bg-[#0e8e0f]' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                    class="icon icon-tabler icons-tabler-outline icon-tabler-settings">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z" />
                    <path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                </svg>
                <span>Settings</span>
            </a>
        </nav>
    </div>

    <!-- LOGOUT -->
    <div>
        <hr class="border-[#E1EFE2] mb-4 -mx-4">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            @method('POST')

            <button type="submit"
                    class="w-full flex items-center justify-center space-x-3 bg-white py-2 rounded-lg text-black font-medium hover:bg-gray-100 transition">
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

</div> --}}