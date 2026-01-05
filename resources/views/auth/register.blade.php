<x-guest-layout>
    <div class="min-h-screen flex flex-col items-center justify-center bg-[#DDEEDB] px-4">

        <!-- Header Icon + Title -->
        <div class="text-center mb-10">
            <div class="bg-[#10AF13] w-14 h-14 rounded-xl flex items-center justify-center mx-auto">
                <svg class="text-white" xmlns="http://www.w3.org/2000/svg" width="35" height="35" viewBox="0 0 24 24" 
                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                    class="icon icon-tabler icons-tabler-outline icon-tabler-school">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M22 9l-10 -4l-10 4l10 4l10 -4v6" />
                    <path d="M6 10.6v5.4a6 3 0 0 0 12 0v-5.4" />
                </svg>
            </div>
            <h2 class="mt-3 text-xl font-semibold">Sistem Pelatihan Guru</h2>
            <p class="text-gray-500 text-sm mt-3">Silahkan register untuk melanjutkan</p>
        </div>
        <div class="bg-white w-full max-w-3xl rounded-lg shadow-lg p-10 grid grid-cols-1 md:grid-cols-2 gap-8">

            <!-- Logo -->
            <div class="flex items-center justify-center">
                <img src="img/logo-timedoor.svg" class="w-100" alt="Logo">
            </div>
            <form method="POST" action="{{ route('register') }}">
                @csrf

                <h3 class="text-center text-lg font-semibold mb-6">Register</h3>

                <!-- Name -->
                <div>
                    <x-input-label for="name" :value="__('Name')" />
                    <x-text-input id="name" class="block mt-1 w-full focus:ring-[#10AF13] focus:border-[#10AF13]" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <!-- Email Address -->
                <div class="mt-4">
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" class="block mt-1 w-full focus:ring-[#10AF13] focus:border-[#10AF13]" type="email" name="email" :value="old('email')" required autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div class="mt-4">
                    <x-input-label for="password" :value="__('Password')" />

                    <div class="relative">
                        <x-text-input id="password" class="block mt-1 w-full focus:ring-[#10AF13] focus:border-[#10AF13]"
                                    type="password"
                                    name="password"
                                    required autocomplete="new-password" />

                        <!-- Icon Mata -->
                        <button type="button" onclick="togglePassword()" 
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-600 hover:text-gray-800 focus:outline-none">

                            <!-- Icon Mata Terbuka -->
                            <svg id="eyeOpenPassword" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10 3C5 3 1.73 7.11 1.07 10c.66 2.89 4 7 8.93 7s8.27-4.11 8.93-7C18.27 7.11 15 3 10 3zM10 15a5 5 0 110-10 5 5 0 010 10z" />
                                <path d="M10 7a3 3 0 100 6 3 3 0 000-6z" />
                            </svg>

                            <!-- Icon Mata Tertutup -->
                            <svg id="eyeClosedPassword" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.956 9.956 0 012.338-4.578M9.88 9.88a3 3 0 104.24 4.24" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6.24 6.239l11.52 11.52" />
                            </svg>
                        </button>
                    </div>

                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Confirm Password -->
                <div class="mt-4">
                    <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

                    <div class="relative">
                        <x-text-input id="password_confirmation" class="block mt-1 w-full focus:ring-[#10AF13] focus:border-[#10AF13]"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

                        <!-- Icon Mata -->
                        <button type="button" onclick="togglePasswordConfirm()" 
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-600 hover:text-gray-800 focus:outline-none">

                            <!-- Icon Mata Terbuka -->
                            <svg id="eyeOpenConfirmPassword" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10 3C5 3 1.73 7.11 1.07 10c.66 2.89 4 7 8.93 7s8.27-4.11 8.93-7C18.27 7.11 15 3 10 3zM10 15a5 5 0 110-10 5 5 0 010 10z" />
                                <path d="M10 7a3 3 0 100 6 3 3 0 000-6z" />
                            </svg>

                            <!-- Icon Mata Tertutup -->
                            <svg id="eyeClosedConfirmPassword" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.956 9.956 0 012.338-4.578M9.88 9.88a3 3 0 104.24 4.24" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6.24 6.239l11.52 11.52" />
                            </svg>
                        </button>
                    </div>

                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                <div class="flex items-center justify-end mt-4">
                    <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-[#10AF13] rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('login') }}">
                        {{ __('Already registered?') }}
                    </a>

                    <x-primary-button class="ms-4 bg-[#10AF13] hover:bg-[#0e8e0f]">
                        {{ __('Register') }}
                    </x-primary-button>
                </div>
            </form>
            <script>
                function togglePassword() {
                    const passwordInput = document.getElementById('password');
                    const passwordConfirmationInput = document.getElementById('password_confirmation');
                    const eyeOpenIcons = document.querySelectorAll('#eyeOpenPassword');
                    const eyeClosedIcons = document.querySelectorAll('#eyeClosedPassword');

                    if (passwordInput.type === 'password') {
                        passwordInput.type = 'text';
                        eyeOpenIcons.forEach(icon => icon.classList.add('hidden'));
                        eyeClosedIcons.forEach(icon => icon.classList.remove('hidden'));
                    } else {
                        passwordInput.type = 'password';
                        eyeOpenIcons.forEach(icon => icon.classList.remove('hidden'));
                        eyeClosedIcons.forEach(icon => icon.classList.add('hidden'));
                    }
                }

                function togglePasswordConfirm() {
                    const passwordConfirmationInput = document.getElementById('password_confirmation');
                    const eyeOpenIcon = passwordConfirmationInput.nextElementSibling.querySelector('#eyeOpenConfirmPassword');
                    const eyeClosedIcon = passwordConfirmationInput.nextElementSibling.querySelector('#eyeClosedConfirmPassword');

                    if (passwordConfirmationInput.type === 'password') {
                        passwordConfirmationInput.type = 'text';
                        eyeOpenIcon.classList.add('hidden');
                        eyeClosedIcon.classList.remove('hidden');
                    } else {
                        passwordConfirmationInput.type = 'password';
                        eyeOpenIcon.classList.remove('hidden');
                        eyeClosedIcon.classList.add('hidden');
                    }
                }
            </script>
        </div>
    </div>
</x-guest-layout>
