<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="id" class="">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIPBO - @yield('title')</title>

    <!-- Anti-flash: HARUS sebelum CSS -->
    <script>
        (function() {
            if (localStorage.getItem('lightMode') === 'true') {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-sipbo-bg dark:bg-light-bg text-sipbo-text dark:text-light-text font-sans antialiased"
    x-data="{
          sidebarOpen: false,
          lightMode: localStorage.getItem('lightMode') === 'true',
          toggleLight() {
              this.lightMode = !this.lightMode;
              localStorage.setItem('lightMode', this.lightMode);
              document.documentElement.classList.toggle('dark', this.lightMode);
          }
      }">

    <div class="flex h-screen overflow-hidden">

        <!-- OVERLAY mobile -->
        <div x-show="sidebarOpen"
            @click="sidebarOpen = false"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-black/50 z-30 lg:hidden"></div>

        <!-- SIDEBAR -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed lg:static lg:translate-x-0 z-40 w-64 h-full flex flex-col
                      bg-sipbo-panel dark:bg-light-sidebar
                      border-r border-sipbo-border dark:border-light-border
                      transition-transform duration-300 ease-in-out">

            <!-- Logo -->
            <div class="p-5 border-b border-sipbo-border dark:border-light-border flex items-center gap-3 flex-shrink-0">
                <div class="w-11 h-11 rounded-full border-2 border-sipbo-gold flex items-center justify-center flex-shrink-0">
                    <span class="text-sipbo-gold font-bold">SP</span>
                </div>
                <div>
                    <h2 class="font-bold text-sipbo-gold text-sm leading-tight">SIPBO</h2>
                    <p class="text-[11px] text-sipbo-text-muted dark:text-light-text-muted leading-tight">Polsek Bojongloa Kidul</p>
                </div>
            </div>

            <!-- Nav -->
            <nav class="flex-1 overflow-y-auto p-3 space-y-0.5">
                @include('layouts.partials.sidebar-nav')
            </nav>

            <!-- Logout -->
            <div class="p-3 border-t border-sipbo-border dark:border-light-border flex-shrink-0">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center gap-3 py-2.5 px-3 rounded-lg text-sm
                                   text-sipbo-text-muted dark:text-light-text-muted
                                   hover:bg-red-900/20 dark:hover:bg-red-50
                                   hover:text-red-400 dark:hover:text-red-600 transition">
                        <i data-lucide="log-out" class="w-4 h-4"></i> Logout
                    </button>
                </form>
            </div>
        </aside>

        <!-- MAIN -->
        <div class="flex-1 flex flex-col overflow-hidden min-w-0">

            <!-- TOPBAR -->
            <header class="flex-shrink-0 bg-sipbo-panel dark:bg-light-panel
                           border-b border-sipbo-border dark:border-light-border
                           px-4 lg:px-6 py-3 flex items-center justify-between gap-3 z-20">

                <div class="flex items-center gap-3 min-w-0">
                    <button @click="sidebarOpen = !sidebarOpen"
                        class="lg:hidden p-2 rounded-lg text-sipbo-text-muted dark:text-light-text-muted
                                   hover:bg-sipbo-panel-light dark:hover:bg-light-panel-light">
                        <i data-lucide="menu" class="w-5 h-5"></i>
                    </button>
                    <h1 class="text-sm lg:text-base font-semibold text-sipbo-text dark:text-light-text truncate">
                        @yield('title')
                    </h1>
                </div>

                <div class="flex items-center gap-2 flex-shrink-0">
                    <!-- Toggle Dark/Light -->
                    <button @click="toggleLight()"
                        class="w-9 h-9 rounded-lg flex items-center justify-center
                                   border border-sipbo-border dark:border-light-border
                                   text-sipbo-text-muted dark:text-light-text-muted
                                   hover:border-sipbo-gold hover:text-sipbo-gold
                                   dark:hover:border-sipbo-gold dark:hover:text-sipbo-gold
                                   transition"
                        :title="lightMode ? 'Ganti ke Mode Gelap' : 'Ganti ke Mode Terang'">
                        <i x-show="!lightMode" data-lucide="sun" class="w-4 h-4"></i>
                        <i x-show="lightMode" data-lucide="moon" class="w-4 h-4"></i>
                    </button>

                    <!-- User info -->
                    <a href="{{ route('profile.password.edit') }}"
                        title="Ubah password akun saya"
                        class="flex items-center gap-2.5 bg-sipbo-gold/10 dark:bg-amber-50
                                border border-sipbo-gold/30 dark:border-amber-200
                                rounded-xl px-3 py-1.5 hover:border-sipbo-gold transition">
                        <div class="w-8 h-8 rounded-full bg-sipbo-gold flex items-center justify-center
                                    font-bold text-sipbo-bg text-sm flex-shrink-0">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                        <div class="hidden sm:block">
                            <p class="text-xs font-semibold text-sipbo-text dark:text-light-text leading-tight">
                                {{ auth()->user()->name }}
                            </p>
                            <p class="text-[10px] text-sipbo-gold leading-tight font-medium">
                                ({{ auth()->user()->role_label }})
                            </p>
                        </div>
                    </a>
                </div>
            </header>

            <!-- ALERTS -->
            @if(session('success') || session('error'))
            <div class="px-4 lg:px-6 pt-4">
                @if(session('success'))
                <div class="flash-success px-4 py-3 rounded-xl text-sm flex items-center gap-2 mb-2">
                    <i data-lucide="check-circle" class="w-4 h-4 flex-shrink-0"></i>
                    {{ session('success') }}
                </div>
                @endif
                @if(session('error'))
                <div class="flash-error px-4 py-3 rounded-xl text-sm flex items-center gap-2">
                    <i data-lucide="x-circle" class="w-4 h-4 flex-shrink-0"></i>
                    {{ session('error') }}
                </div>
                @endif
            </div>
            @endif

            <!-- CONTENT -->
            <main class="flex-1 overflow-y-auto p-4 lg:p-6">
                @yield('content')
            </main>

            <!-- FOOTER -->
            <footer class="flex-shrink-0 bg-sipbo-panel dark:bg-light-panel
                           border-t border-sipbo-border dark:border-light-border
                           text-center py-3 text-xs text-sipbo-text-muted dark:text-light-text-muted">
                SIPBO &copy; {{ date('Y') }} | Polsek Bojongloa Kidul
            </footer>
        </div>
    </div>

    {{-- Modal konfirmasi global — pengganti browser confirm() di semua tombol --}}
    <x-modal id="confirm-global" title="Konfirmasi" size="sm">
        <p id="confirm-modal-message" class="text-sm text-sipbo-text dark:text-light-text mb-5"></p>
        <div class="flex justify-end gap-3">
            <x-btn-secondary type="button" onclick="closeModal('confirm-global')">Batal</x-btn-secondary>
            <button id="confirm-modal-confirm-btn" type="button" onclick="window.__submitConfirmedAction()"
                class="px-4 py-2 rounded-xl text-sm font-semibold transition
                       bg-sipbo-gold hover:bg-sipbo-gold-light text-sipbo-bg">
                Ya, Lanjutkan
            </button>
        </div>
    </x-modal>

    @stack('scripts')
</body>

</html>