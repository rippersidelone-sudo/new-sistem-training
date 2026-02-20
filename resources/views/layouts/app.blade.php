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
        [x-cloak] { display: none !important; }

        @media (min-width: 1024px) {
            .sidebar-container { transform: translateX(0) !important; }
        }

        body { opacity: 1; transition: opacity 0.1s ease-in; }
        html { overflow-y: scroll; }
    </style>

    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen">

        @yield('sidebar')

        <main class="lg:ml-64 min-h-screen p-8">
            @yield('content')
        </main>

        {{-- =============================================
             GLOBAL TOAST HUB
             Dipanggil via: window.toast({ type, title, message })
             ============================================= --}}
        <div x-data="toastHub()"
             x-init="init()"
             class="fixed bottom-6 right-6 z-[9999] w-full max-w-sm space-y-3 pointer-events-none">
            <template x-for="t in toasts" :key="t.id">
                <div x-show="t.show"
                     x-cloak
                     x-transition:enter="transition ease-out duration-250"
                     x-transition:enter-start="opacity-0 translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 translate-y-2"
                     class="pointer-events-auto">
                    <div class="flex items-start gap-3 px-4 py-3 rounded-xl shadow-2xl border border-white/20"
                         :class="{
                             'bg-[#10AF13] text-white' : t.type === 'success',
                             'bg-red-600 text-white'   : t.type === 'error',
                             'bg-yellow-500 text-white': t.type === 'warning',
                             'bg-blue-600 text-white'  : t.type === 'info'
                         }">
                        {{-- Icon --}}
                        <div class="mt-0.5 shrink-0">
                            <template x-if="t.type === 'success'">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 6 9 17l-5-5" />
                                </svg>
                            </template>
                            <template x-if="t.type === 'error'">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M18 6 6 18" /><path d="M6 6l12 12" />
                                </svg>
                            </template>
                            <template x-if="t.type === 'warning' || t.type === 'info'">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10" />
                                    <path d="M12 8v4M12 16h.01" />
                                </svg>
                            </template>
                        </div>

                        {{-- Text --}}
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold" x-text="t.title"></p>
                            <p class="text-sm opacity-95 mt-0.5 leading-snug" x-text="t.message"></p>
                        </div>

                        {{-- Close --}}
                        <button type="button" @click="remove(t.id)"
                                class="opacity-80 hover:opacity-100 transition shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M18 6 6 18" /><path d="M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </template>
        </div>

    </div>

    @stack('scripts')

    {{-- =============================================
         TOAST HUB SCRIPT — hanya satu, di sini saja
         ============================================= --}}
    <script>
    function toastHub() {
        return {
            toasts: [],
            _id: 1,

            init() {
                // Expose global window.toast()
                window.toast = (options = {}) => {
                    const type    = options.type    || 'success';
                    const title   = options.title   || ({ success: 'Berhasil', error: 'Gagal', warning: 'Peringatan', info: 'Info' }[type] ?? 'Notifikasi');
                    const message = options.message || '';
                    const timeout = Number(options.timeout ?? 3500);

                    const id = this._id++;
                    this.toasts.push({ id, type, title, message, show: true });

                    if (timeout > 0) {
                        setTimeout(() => this.remove(id), timeout);
                    }
                };

                // Flush session flash yang di-pass via meta (opsional, lihat catatan)
            },

            remove(id) {
                const idx = this.toasts.findIndex(t => t.id === id);
                if (idx === -1) return;
                this.toasts[idx].show = false;
                setTimeout(() => {
                    this.toasts = this.toasts.filter(t => t.id !== id);
                }, 220);
            }
        }
    }
    </script>

    {{-- =============================================
         SESSION FLASH → window.toast()
         Sentralisasi di sini, TIDAK perlu di layout role
         ============================================= --}}
    @if(session('success'))
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        window.toast({ type: 'success', title: 'Berhasil', message: @js(session('success')) });
    });
    </script>
    @endif

    @if(session('error'))
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        window.toast({ type: 'error', title: 'Gagal', message: @js(session('error')) });
    });
    </script>
    @endif

    @if(session('warning'))
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        window.toast({ type: 'warning', title: 'Peringatan', message: @js(session('warning')) });
    });
    </script>
    @endif

    @if(session('info'))
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        window.toast({ type: 'info', title: 'Info', message: @js(session('info')) });
    });
    </script>
    @endif

    {{-- Validation errors: satu toast per field, muncul bertumpuk dengan delay --}}
    @if($errors->any() && !session('error'))
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const errors = @js($errors->all());
        errors.forEach((message, index) => {
            setTimeout(() => {
                window.toast({
                    type   : 'error',
                    title  : 'Validasi Gagal',
                    message: message,
                    timeout: 5000 + (index * 500), {{-- toast lebih lama semakin banyak error --}}
                });
            }, index * 150); {{-- delay antar toast supaya animasi terlihat bertumpuk --}}
        });
    });
    </script>
    @endif

</body>
</html>