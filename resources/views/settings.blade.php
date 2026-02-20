{{-- resources/views/settings.blade.php --}}
@php
    $user = auth()->user();
    $role = $user->role?->name;

    $layout = match(true) {
        $role === 'HQ Admin'             => 'layouts.admin',
        $role === 'Training Coordinator' => 'layouts.coordinator',
        $role === 'Trainer'              => 'layouts.trainer',
        $role === 'Branch Coordinator'   => 'layouts.branch-pic',
        $role === 'Participant'          => 'layouts.participant',
        default                          => 'layouts.app',
    };
@endphp

@extends($layout)

@section('content')
    <div class="px-2">
        <h1 class="text-2xl font-semibold text-gray-800">Profile Settings</h1>
        <p class="text-[#737373] mt-2 font-medium">Kelola profile dan akun Anda</p>
    </div>

    <div class="py-8 space-y-6">

        {{-- Profile Card --}}
        <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden">

            {{-- Header hijau --}}
            <div class="bg-[#10AF13] px-6 py-5">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center text-[#10AF13] font-bold text-2xl shadow-md shrink-0">
                        {{ strtoupper(substr($user->name ?? '', 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-lg font-bold text-white">{{ $user->name }}</p>
                        <p class="text-sm text-white/80">{{ $user->role?->name ?? '-' }}</p>
                        @if($user->branch)
                            <p class="text-xs text-white/70 mt-0.5">{{ $user->branch->name }}</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Form Profile --}}
            <div class="p-6 sm:p-8">
                <h2 class="text-base font-semibold text-gray-800 mb-1">Informasi Profil</h2>
                <p class="text-sm text-gray-500 mb-6">Perbarui nama dan email akun Anda.</p>

                <form method="post" action="{{ route('profile.update') }}" class="space-y-5 max-w-lg">
                    @csrf
                    @method('patch')

                    {{-- Nama --}}
                    <div>
                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-1.5">Nama</label>
                        <input id="name" name="name" type="text"
                               value="{{ old('name', $user->name) }}"
                               required autofocus autocomplete="name"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-[#10AF13] focus:ring-2 focus:ring-[#10AF13]/20 transition" />
                        @error('name')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">Email</label>
                        <input id="email" name="email" type="email"
                               value="{{ old('email', $user->email) }}"
                               required autocomplete="username"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-[#10AF13] focus:ring-2 focus:ring-[#10AF13]/20 transition" />
                        @error('email')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Cabang (Branch Coordinator & Participant) --}}
                    @if($user->role && in_array($user->role->name, ['Branch Coordinator', 'Participant']))
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Cabang</label>
                            <input type="text"
                                   value="{{ $user->branch?->name ?? '-' }}"
                                   disabled
                                   class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm bg-gray-100 text-gray-500 cursor-not-allowed" />
                            <p class="text-xs text-gray-400 mt-1">Informasi cabang tidak dapat diubah.</p>
                        </div>
                    @endif

                    {{-- Role --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Role</label>
                        <input type="text"
                               value="{{ $user->role?->name ?? '-' }}"
                               disabled
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm bg-gray-100 text-gray-500 cursor-not-allowed" />
                        <p class="text-xs text-gray-400 mt-1">Role tidak dapat diubah. Hubungi admin jika diperlukan.</p>
                    </div>

                    <div class="flex items-center gap-4 pt-2">
                        <button type="submit"
                                class="px-6 py-2.5 bg-[#10AF13] text-white text-sm font-semibold rounded-lg hover:bg-[#0e9e10] shadow-md shadow-[#10AF13]/20 transition">
                            Simpan
                        </button>
                        @if(session('status') === 'profile-updated')
                            <p x-data="{ show: true }"
                               x-show="show"
                               x-transition
                               x-init="setTimeout(() => show = false, 2000)"
                               class="text-sm text-[#10AF13] font-medium">
                                Tersimpan.
                            </p>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        {{-- Update Password --}}
        <div class="bg-white border border-gray-200 rounded-2xl p-6 sm:p-8">
            <h2 class="text-base font-semibold text-gray-800 mb-1">Ubah Password</h2>
            <p class="text-sm text-gray-500 mb-6">Gunakan password yang panjang dan acak agar akun tetap aman.</p>

            <form method="post" action="{{ route('password.update') }}" class="space-y-5 max-w-lg">
                @csrf
                @method('put')

                <div>
                    <label for="current_password" class="block text-sm font-semibold text-gray-700 mb-1.5">Password Saat Ini</label>
                    <input id="current_password" name="current_password" type="password"
                           autocomplete="current-password"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-[#10AF13] focus:ring-2 focus:ring-[#10AF13]/20 transition" />
                    @if($errors->updatePassword->get('current_password'))
                        <p class="text-xs text-red-500 mt-1">{{ $errors->updatePassword->first('current_password') }}</p>
                    @endif
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-1.5">Password Baru</label>
                    <input id="password" name="password" type="password"
                           autocomplete="new-password"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-[#10AF13] focus:ring-2 focus:ring-[#10AF13]/20 transition" />
                    @if($errors->updatePassword->get('password'))
                        <p class="text-xs text-red-500 mt-1">{{ $errors->updatePassword->first('password') }}</p>
                    @endif
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-1.5">Konfirmasi Password Baru</label>
                    <input id="password_confirmation" name="password_confirmation" type="password"
                           autocomplete="new-password"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-[#10AF13] focus:ring-2 focus:ring-[#10AF13]/20 transition" />
                    @if($errors->updatePassword->get('password_confirmation'))
                        <p class="text-xs text-red-500 mt-1">{{ $errors->updatePassword->first('password_confirmation') }}</p>
                    @endif
                </div>

                <div class="flex items-center gap-4 pt-2">
                    <button type="submit"
                            class="px-6 py-2.5 bg-[#10AF13] text-white text-sm font-semibold rounded-lg hover:bg-[#0e9e10] shadow-md shadow-[#10AF13]/20 transition">
                        Simpan Password
                    </button>
                    @if(session('status') === 'password-updated')
                        <p x-data="{ show: true }"
                           x-show="show"
                           x-transition
                           x-init="setTimeout(() => show = false, 2000)"
                           class="text-sm text-[#10AF13] font-medium">
                            Password berhasil diperbarui.
                        </p>
                    @endif
                </div>
            </form>
        </div>

        {{-- Hapus Akun --}}
        <div class="bg-white border border-red-100 rounded-2xl p-6 sm:p-8">
            <h2 class="text-base font-semibold text-gray-800 mb-1">Hapus Akun</h2>
            <p class="text-sm text-gray-500 mb-6">
                Setelah akun dihapus, semua data akan dihapus secara permanen.
                Pastikan Anda sudah mengunduh data penting sebelum melanjutkan.
            </p>

            {{-- Trigger Button --}}
            <button
                x-data=""
                x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
                class="px-6 py-2.5 bg-red-500 text-white text-sm font-semibold rounded-lg hover:bg-red-600 transition shadow-md shadow-red-500/20">
                Hapus Akun
            </button>
        </div>

    </div>

    {{-- ============================================================
         MODAL KONFIRMASI HAPUS AKUN — desain putih/bersih
         ============================================================ --}}
    <div
        x-data="{ show: {{ $errors->userDeletion->isNotEmpty() ? 'true' : 'false' }} }"
        x-show="show"
        x-cloak
        @open-modal.window="if ($event.detail === 'confirm-user-deletion') show = true"
        @close.window="show = false"
        @keydown.escape.window="show = false"
        class="fixed inset-0 z-[9998] flex items-center justify-center p-4"
    >
        {{-- Backdrop --}}
        <div
            x-show="show"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="show = false"
            class="absolute inset-0 bg-gray-500/40 backdrop-blur-sm"
        ></div>

        {{-- Panel --}}
        <div
            x-show="show"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="relative w-full max-w-md bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden"
        >
            {{-- Top accent strip --}}
            <div class="h-1.5 w-full bg-gradient-to-r from-red-400 to-red-600"></div>

            <div class="p-6">
                {{-- Icon + Judul --}}
                <div class="flex items-start gap-4 mb-5">
                    <div class="w-11 h-11 rounded-xl bg-red-50 border border-red-100 flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                             class="text-red-500">
                            <path d="M3 6h18" />
                            <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6" />
                            <path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                            <line x1="10" y1="11" x2="10" y2="17" />
                            <line x1="14" y1="11" x2="14" y2="17" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-gray-900">Hapus Akun Anda?</h2>
                        <p class="text-sm text-gray-500 mt-0.5 leading-relaxed">
                            Tindakan ini <span class="font-semibold text-red-500">tidak dapat dibatalkan</span>.
                            Semua data akun akan dihapus permanen.
                        </p>
                    </div>
                </div>

                {{-- Peringatan --}}
                <div class="bg-red-50 border border-red-100 rounded-xl px-4 py-3 mb-5 flex gap-2.5">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         class="text-red-500 shrink-0 mt-0.5">
                        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                        <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                    </svg>
                    <p class="text-xs text-red-600 leading-relaxed">
                        Masukkan password Anda untuk mengkonfirmasi penghapusan akun ini.
                    </p>
                </div>

                {{-- Form --}}
                <form method="post" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('delete')

                    <div class="mb-5">
                        <label for="del_password" class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Password
                        </label>
                        <input id="del_password" name="password" type="password"
                               placeholder="Masukkan password Anda"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-red-400 focus:ring-2 focus:ring-red-400/20 transition" />
                        @if($errors->userDeletion->get('password'))
                            <p class="text-xs text-red-500 mt-1.5 flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="2.5">
                                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                                </svg>
                                {{ $errors->userDeletion->first('password') }}
                            </p>
                        @endif
                    </div>

                    {{-- Actions --}}
                    <div class="flex gap-3">
                        <button type="button"
                                @click="show = false"
                                class="flex-1 px-4 py-2.5 border border-gray-200 rounded-lg text-sm text-gray-600 font-medium hover:bg-gray-50 transition">
                            Batal
                        </button>
                        <button type="submit"
                                class="flex-1 px-4 py-2.5 bg-red-500 text-white rounded-lg text-sm font-semibold hover:bg-red-600 transition shadow-sm shadow-red-500/30 flex items-center justify-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 6h18"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                <path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                            </svg>
                            Ya, Hapus Akun
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection