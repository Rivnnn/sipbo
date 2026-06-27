<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIPBO - @yield('title')</title>

    {{-- ① Anti-flash: SEBELUM apapun di-render --}}
    <script>
        (function() {
            var t = localStorage.getItem('theme') ?? 'dark';
            if (t === 'dark') document.documentElement.classList.add('dark');
        })();
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-sipbo-bg dark:bg-light-bg text-sipbo-text dark:text-light-text font-sans antialiased">

    <div class="flex h-screen overflow-hidden" x-data="{ sidebarOpen: false }">

        <!-- OVERLAY mobile -->
        <div x-show="sidebarOpen" @click="sidebarOpen = false"
            x-transition:enter="transition-opacity ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-black/60 z-30 lg:hidden">
        </div>

        <!-- ══ SIDEBAR ══════════════════════════════════════════ -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed lg:static lg:translate-x-0 z-40 w-64 h-full flex flex-col
                  bg-sipbo-panel dark:bg-light-sidebar
                  border-r border-sipbo-border dark:border-light-border
                  transition-transform duration-300 [transition-timing-function:cubic-bezier(0.22,1,0.36,1)]">
            <!-- Logo -->
            <div class="px-4 py-4 border-b border-sipbo-border dark:border-light-border
                    flex items-center gap-3 flex-shrink-0">
                <div class="w-9 h-9 rounded-full border-2 border-sipbo-gold
                        flex items-center justify-center flex-shrink-0">
                    <span class="text-sipbo-gold text-xs font-bold">SP</span>
                </div>
                <div class="overflow-hidden">
                    <p class="font-bold text-sipbo-gold text-sm truncate leading-tight">SIPBO</p>
                    <p class="text-[10px] text-sipbo-text-muted dark:text-light-text-muted truncate">
                        Polsek Bojongloa Kidul
                    </p>
                </div>
            </div>

            <!-- Nav -->
            <nav class="flex-1 overflow-y-auto p-2 space-y-0.5">
                @include('layouts.partials.sidebar-nav')
            </nav>

            <!-- Logout -->
            <div class="p-2 border-t border-sipbo-border dark:border-light-border flex-shrink-0">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="w-full flex items-center gap-3 py-2.5 px-3 rounded-lg text-sm
                               text-sipbo-text-muted dark:text-light-text-muted
                               hover:bg-red-900/20 dark:hover:bg-red-50
                               hover:text-red-400 dark:hover:text-red-600 transition">
                        <i data-lucide="log-out" class="w-4 h-4 flex-shrink-0"></i> Logout
                    </button>
                </form>
            </div>
        </aside>

        <!-- ══ MAIN ═════════════════════════════════════════════ -->
        <div class="flex-1 flex flex-col overflow-hidden min-w-0">

            <!-- TOPBAR -->
            <header class="sticky top-0 z-20 flex-shrink-0
                       bg-sipbo-panel dark:bg-light-panel
                       border-b border-sipbo-border dark:border-light-border dark:shadow-sm
                       px-4 lg:px-6 h-14 flex items-center justify-between gap-3">

                <div class="flex items-center gap-3 min-w-0">
                    <button @click="sidebarOpen = !sidebarOpen"
                        class="lg:hidden p-1.5 rounded-lg
                           text-sipbo-text-muted dark:text-light-text-muted
                           hover:text-sipbo-gold hover:bg-sipbo-panel-light
                           dark:hover:bg-light-panel-light transition">
                        <i data-lucide="menu" class="w-5 h-5"></i>
                    </button>
                    <h1 class="text-sm lg:text-base font-semibold truncate
                           text-sipbo-text dark:text-light-text">
                        @yield('title')
                    </h1>
                </div>

                <div class="flex items-center gap-2 flex-shrink-0">

                    {{-- ② Theme toggle — ikon dikelola JS setelah lucide.createIcons() --}}
                    <button id="theme-toggle" title="Ganti tema"
                        class="w-8 h-8 flex items-center justify-center rounded-lg
                           border border-sipbo-border dark:border-light-border
                           text-sipbo-text-muted dark:text-light-text-muted
                           hover:text-sipbo-gold hover:border-sipbo-gold/60
                           dark:hover:text-sipbo-gold dark:hover:border-sipbo-gold/60 transition">
                        {{-- Kedua ikon ada di DOM; JS yang atur display --}}
                        <i data-lucide="sun" id="icon-sun" class="w-4 h-4"></i>
                        <i data-lucide="moon" id="icon-moon" class="w-4 h-4"></i>
                    </button>

                    {{-- User badge --}}
                    <div class="flex items-center gap-2
                            bg-sipbo-gold/10 border border-sipbo-gold/30
                            rounded-xl px-3 py-1.5">
                        <div class="w-7 h-7 rounded-full bg-sipbo-gold
                                flex items-center justify-center
                                font-bold text-sipbo-bg text-xs flex-shrink-0">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                        <div class="hidden sm:block">
                            <p class="text-xs font-semibold leading-tight
                                  text-sipbo-text dark:text-light-text">
                                {{ auth()->user()->name }}
                            </p>
                            <p class="text-[10px] text-sipbo-gold leading-tight">
                                {{ auth()->user()->role_label }}
                            </p>
                        </div>
                    </div>
                </div>
            </header>

            <!-- CONTENT -->
            <main class="flex-1 overflow-y-auto p-4 lg:p-6 bg-sipbo-bg dark:bg-light-bg">

                @if(session('success'))
                <div class="flash-success px-4 py-3 rounded-xl mb-4 text-sm flex items-center gap-2">
                    <i data-lucide="check-circle" class="w-4 h-4 flex-shrink-0"></i>
                    {{ session('success') }}
                </div>
                @endif

                @if(session('error'))
                <div class="flash-error px-4 py-3 rounded-xl mb-4 text-sm flex items-center gap-2">
                    <i data-lucide="x-circle" class="w-4 h-4 flex-shrink-0"></i>
                    {{ session('error') }}
                </div>
                @endif

                @yield('content')
            </main>

            <!-- FOOTER -->
            <footer class="flex-shrink-0 text-center py-2.5 text-[11px]
                       bg-sipbo-panel dark:bg-light-panel
                       border-t border-sipbo-border dark:border-light-border
                       text-sipbo-text-muted dark:text-light-text-muted">
                SIPBO &copy; {{ date('Y') }} &nbsp;|&nbsp; Polsek Bojongloa Kidul
            </footer>
        </div>
    </div>

    {{-- Alpine --}}
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    {{-- Lucide --}}
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>

    <script>
        // ③ Jalankan setelah DOM siap
        document.addEventListener('DOMContentLoaded', function() {

            // ── Setup toggle DULUAN — independen dari lucide ──
            var html = document.documentElement;
            var btn = document.getElementById('theme-toggle');
            var sun = document.getElementById('icon-sun');
            var moon = document.getElementById('icon-moon');

            function syncIcons() {
                if (!sun || !moon) return;
                var dark = html.classList.contains('dark');
                sun.style.display = dark ? '' : 'none';
                moon.style.display = dark ? 'none' : '';
            }

            btn && btn.addEventListener('click', function() {
                var nowDark = html.classList.toggle('dark');
                localStorage.setItem('theme', nowDark ? 'dark' : 'light');
                syncIcons();
                window.dispatchEvent(new Event('themechange'));
            });

            // ── Render ikon lucide — boleh gagal tanpa merusak toggle ──
            try {
                lucide.createIcons();
            } catch (e) {
                console.warn('Lucide gagal dimuat:', e);
            }

            // setelah createIcons, <i> sudah jadi <svg> tapi id-nya tetap sama
            sun = document.getElementById('icon-sun') || sun;
            moon = document.getElementById('icon-moon') || moon;
            syncIcons();
        });
    </script>

    @stack('scripts')
</body>

</html>