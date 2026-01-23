<x-guest-layout>

    <div class="min-h-screen flex items-center justify-center bg-[#d4e8d2] px-4 py-6">
        
        <div class="w-full max-w-4xl">
            <!-- Header Section -->
            <div class="text-center mb-6">
                <div class="bg-[#10AF13] w-12 h-12 rounded-xl flex items-center justify-center mx-auto mb-3 shadow-lg">
                    <svg class="text-white" xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M22 9l-10 -4l-10 4l10 4l10 -4v6" />
                        <path d="M6 10.6v5.4a6 3 0 0 0 12 0v-5.4" />
                    </svg>
                </div>
                <h1 class="text-xl font-bold text-gray-800 mb-1">Training Next Level System</h1>
                <p class="text-gray-600 text-sm">Silahkan login untuk melanjutkan</p>
            </div>

            <!-- Main Card -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="grid grid-cols-1 md:grid-cols-2">
                    
                    <!-- Left Side - Logo -->
                    <div class="flex items-center justify-center p-8 bg-white">
                        <div class="w-full max-w-sm">
                            <img src="{{ asset('img/logo-timedoor.svg') }}" alt="Timedoor Academy" class="w-full">
                        </div>
                    </div>

                    <!-- Right Side - Form -->
                    <div class="p-8 flex items-center">
                        <div class="w-full"
                             x-data="{
                                 selectedRole: '{{ old('role', '') }}',
                                 selectedRoleLabel: '{{ old('role', 'Pilih Role') }}',
                                 showToken: {{ old('role') && old('role') !== 'Participant' ? 'true' : 'false' }},
                                 togglePassword: false,
                                 toggleTokenVisibility: false,
                                 roleDropdownOpen: false,
                                 selectRole(value, label) {
                                     this.selectedRole = value;
                                     this.selectedRoleLabel = label;
                                     this.showToken = (value !== '' && value !== 'Participant');
                                     this.roleDropdownOpen = false;
                                 },
                                 clearForm() {
                                     document.getElementById('email').value = '';
                                     document.getElementById('password').value = '';
                                     document.getElementById('token').value = '';
                                     this.selectedRole = '';
                                     this.selectedRoleLabel = 'Pilih Role';
                                     this.showToken = false;
                                     document.getElementById('remember_me').checked = false;
                                 }
                             }"
                             x-init="
                                // Clear form on page load if there's an error
                                @if($errors->any())
                                    $nextTick(() => clearForm());
                                @endif
                             ">
                            <h2 class="text-xl font-bold text-gray-900 mb-6 text-center">Login</h2>

                            <!-- Session Status -->
                            <x-auth-session-status class="mb-4" :status="session('status')" />

                            <form method="POST" action="{{ route('login') }}" class="space-y-4" autocomplete="off">
                                @csrf

                                <!-- Email -->
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                    <input id="email" type="email" name="email" value=""
                                           required autofocus autocomplete="off"
                                           placeholder="Masukkan email"
                                           class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#10AF13] focus:border-transparent transition duration-150">
                                    @error('email')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Password -->
                                <div>
                                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                                    <div class="relative">
                                        <input id="password" 
                                               :type="togglePassword ? 'text' : 'password'"
                                               name="password" required autocomplete="new-password"
                                               placeholder="Masukkan password"
                                               class="w-full px-4 py-2.5 pr-12 bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#10AF13] focus:border-transparent transition duration-150">
                                        <button type="button" @click="togglePassword = !togglePassword"
                                                class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 hover:text-gray-600 focus:outline-none z-10">
                                            <svg x-show="!togglePassword" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            <svg x-show="togglePassword" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" x-cloak>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.956 9.956 0 012.338-4.578M9.88 9.88a3 3 0 104.24 4.24M15 12a3 3 0 11-6 0 3 3 0 016 0zM3 3l18 18" />
                                            </svg>
                                        </button>
                                    </div>
                                    @error('password')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Role Selection - Custom Dropdown Style -->
                                <div class="relative">
                                    <label for="role" class="block text-sm font-medium text-gray-700 mb-2">Login sebagai</label>
                                    <div class="relative z-20">
                                        <!-- Dropdown Button -->
                                        <button type="button" 
                                                @click="roleDropdownOpen = !roleDropdownOpen"
                                                :class="roleDropdownOpen ? 'border-[#10AF13] ring-2 ring-[#10AF13]/20' : 'border-gray-300'"
                                                class="w-full px-4 py-2.5 rounded-lg border cursor-pointer flex justify-between items-center text-sm bg-white transition focus:outline-none">
                                            <span x-text="selectedRoleLabel" :class="selectedRole === '' ? 'text-gray-400' : 'text-gray-900'"></span>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                class="flex-shrink-0 ml-2 transition-transform"
                                                :class="roleDropdownOpen ? 'rotate-180' : ''">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M6 9l6 6l6 -6" />
                                            </svg>
                                        </button>

                                        <!-- Dropdown Content -->
                                        <div x-show="roleDropdownOpen" 
                                             @click.outside="roleDropdownOpen = false" 
                                             x-transition:enter="transition ease-out duration-200"
                                             x-transition:enter-start="opacity-0 scale-95" 
                                             x-transition:enter-end="opacity-100 scale-100"
                                             x-transition:leave="transition ease-in duration-150" 
                                             x-transition:leave-start="opacity-100 scale-100"
                                             x-transition:leave-end="opacity-0 scale-95"
                                             style="position: fixed; z-index: 9999; max-height: 180px;"
                                             x-ref="dropdown"
                                             @resize.window="
                                                if(roleDropdownOpen) {
                                                    const button = $el.previousElementSibling;
                                                    const rect = button.getBoundingClientRect();
                                                    $el.style.top = rect.bottom + window.scrollY + 8 + 'px';
                                                    $el.style.left = rect.left + 'px';
                                                    $el.style.width = rect.width + 'px';
                                                }
                                             "
                                             x-init="
                                                $watch('roleDropdownOpen', value => {
                                                    if(value) {
                                                        $nextTick(() => {
                                                            const button = $el.previousElementSibling;
                                                            const rect = button.getBoundingClientRect();
                                                            $el.style.top = rect.bottom + window.scrollY + 8 + 'px';
                                                            $el.style.left = rect.left + 'px';
                                                            $el.style.width = rect.width + 'px';
                                                        });
                                                    }
                                                })
                                             "
                                             class="bg-white border border-gray-200 rounded-xl shadow-2xl overflow-y-auto"
                                             x-cloak>

                                            @php
                                                // Urutan role dari yang paling penting
                                                $roleOrder = ['Master HQ', 'Training Coordinator', 'Branch Coordinator', 'Trainer', 'Participant'];
                                                $sortedRoles = $roles->sortBy(function($role) use ($roleOrder) {
                                                    $index = array_search($role->name, $roleOrder);
                                                    return $index !== false ? $index : 999;
                                                });
                                            @endphp

                                            @foreach($sortedRoles as $role)
                                            <div @click="selectRole('{{ $role->name }}', '{{ $role->name }}')"
                                                class="px-4 py-3 text-sm cursor-pointer flex justify-between items-center hover:bg-gray-50 transition">
                                                <span class="font-medium text-gray-700">{{ $role->name }}</span>
                                                <svg x-show="selectedRole === '{{ $role->name }}'" 
                                                    xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" 
                                                    stroke="#10AF13" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    x-cloak>
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                    <path d="M5 12l5 5l10 -10" />
                                                </svg>
                                            </div>
                                            @endforeach
                                        </div>

                                        <input type="hidden" name="role" :value="selectedRole">
                                    </div>
                                    @error('role')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Token Field - dengan icon mata -->
                                <div x-show="showToken" 
                                     x-transition:enter="transition ease-out duration-300"
                                     x-transition:enter-start="opacity-0 -translate-y-2" 
                                     x-transition:enter-end="opacity-100 translate-y-0"
                                     class="transition-all duration-300 ease-in-out"
                                     x-cloak>
                                    <label for="token" class="block text-sm font-medium text-gray-700 mb-2">Token Akses</label>
                                    <div class="relative">
                                        <input id="token" 
                                               :type="toggleTokenVisibility ? 'text' : 'password'"
                                               name="token" value=""
                                               autocomplete="off" placeholder="Masukkan token akses"
                                               class="w-full px-4 py-2.5 pr-12 bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#10AF13] focus:border-transparent transition duration-150 password-no-reveal">
                                        <button type="button" @click="toggleTokenVisibility = !toggleTokenVisibility"
                                                class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 hover:text-gray-600 focus:outline-none z-10">
                                            <svg x-show="!toggleTokenVisibility" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            <svg x-show="toggleTokenVisibility" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" x-cloak>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.956 9.956 0 012.338-4.578M9.88 9.88a3 3 0 104.24 4.24M15 12a3 3 0 11-6 0 3 3 0 016 0zM3 3l18 18" />
                                            </svg>
                                        </button>
                                    </div>
                                    @error('token')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-1 text-xs text-gray-500">Token diperlukan untuk role selain Participant</p>
                                </div>

                                <!-- Remember Me -->
                                <div class="flex items-center">
                                    <input id="remember_me" type="checkbox" name="remember"
                                           class="h-4 w-4 text-[#10AF13] border-gray-300 rounded focus:ring-[#10AF13]">
                                    <label for="remember_me" class="ml-2 block text-sm text-gray-700 cursor-pointer">Remember me</label>
                                </div>

                                <!-- Submit Button -->
                                <button type="submit"
                                        class="w-full bg-[#10AF13] hover:bg-[#0d8f11] text-white font-bold py-2.5 px-4 rounded-lg transition duration-200 ease-in-out transform hover:scale-[1.01] focus:outline-none focus:ring-2 focus:ring-[#10AF13] focus:ring-offset-2 uppercase tracking-wide">
                                    LOGIN
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Hide browser's default password reveal button */
        input[type="password"]::-ms-reveal,
        input[type="password"]::-ms-clear,
        .password-no-reveal::-ms-reveal,
        .password-no-reveal::-ms-clear {
            display: none;
        }
        
        input[type="password"]::-webkit-contacts-auto-fill-button,
        input[type="password"]::-webkit-credentials-auto-fill-button,
        .password-no-reveal::-webkit-contacts-auto-fill-button,
        .password-no-reveal::-webkit-credentials-auto-fill-button {
            visibility: hidden;
            pointer-events: none;
            position: absolute;
            right: 0;
        }

        /* Custom scrollbar untuk dropdown role */
        [x-ref="dropdown"]::-webkit-scrollbar {
            width: 6px;
        }

        [x-ref="dropdown"]::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        [x-ref="dropdown"]::-webkit-scrollbar-thumb {
            background: #10AF13;
            border-radius: 10px;
        }

        [x-ref="dropdown"]::-webkit-scrollbar-thumb:hover {
            background: #0e8e0f;
        }
    </style>

</x-guest-layout>