{{-- resources/views/layouts/partials/sidebar-nav.blade.php --}}

{{-- DASHBOARD --}}
@can('dashboard.lihat')
<a href="{{ route('dashboard.index') }}"
    class="flex items-center gap-3 py-2.5 px-3 rounded-lg text-sm transition
          {{ request()->routeIs('dashboard.*')
             ? 'bg-sipbo-gold text-sipbo-bg font-semibold'
             : 'text-sipbo-text-muted dark:text-light-text-muted hover:bg-sipbo-panel-light dark:hover:bg-light-panel-light hover:text-sipbo-text dark:hover:text-light-text' }}">
    <i data-lucide="layout-dashboard" class="w-4 h-4 flex-shrink-0"></i>
    <span>Dashboard</span>
</a>
@endcan

{{-- DIVIDER --}}
<div class="my-2 border-t border-sipbo-border dark:border-light-border opacity-70"></div>

{{-- PENGAJUAN ANGGARAN (Staf) --}}
@can('pengajuan.create')
<div x-data="{ open: {{ request()->routeIs('staf.pengajuan.*') ? 'true' : 'false' }} }">
    <button @click="open = !open"
        class="w-full flex items-center justify-between py-2.5 px-3 rounded-lg text-sm transition
               {{ request()->routeIs('staf.pengajuan.*')
                  ? 'text-sipbo-gold dark:text-sipbo-gold'
                  : 'text-sipbo-text-muted dark:text-light-text-muted hover:bg-sipbo-panel-light dark:hover:bg-light-panel-light hover:text-sipbo-text dark:hover:text-light-text' }}">
        <span class="flex items-center gap-3">
            <i data-lucide="file-text" class="w-4 h-4 flex-shrink-0"></i>
            <span>Pengajuan Anggaran</span>
        </span>
        <i data-lucide="chevron-down"
            class="w-3.5 h-3.5 flex-shrink-0 transition-transform duration-200"
            :class="open ? 'rotate-180 text-sipbo-gold' : ''"></i>
    </button>

    <div x-show="open"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 -translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-1"
        class="mt-1 ml-4 pl-3 border-l border-sipbo-border dark:border-light-border space-y-0.5">

        <a href="{{ route('staf.pengajuan.create') }}"
            class="flex items-center gap-2 py-2 px-3 rounded-lg text-sm transition
                  {{ request()->routeIs('staf.pengajuan.create')
                     ? 'text-sipbo-gold font-medium bg-sipbo-gold/10'
                     : 'text-sipbo-text-muted dark:text-light-text-muted hover:text-sipbo-text dark:hover:text-light-text hover:bg-sipbo-panel-light dark:hover:bg-light-panel-light' }}">
            <i data-lucide="plus-circle" class="w-3.5 h-3.5 flex-shrink-0"></i> Baru
        </a>

        <a href="{{ route('staf.pengajuan.index') }}"
            class="flex items-center gap-2 py-2 px-3 rounded-lg text-sm transition
                  {{ request()->routeIs('staf.pengajuan.index')
                     ? 'text-sipbo-gold font-medium bg-sipbo-gold/10'
                     : 'text-sipbo-text-muted dark:text-light-text-muted hover:text-sipbo-text dark:hover:text-light-text hover:bg-sipbo-panel-light dark:hover:bg-light-panel-light' }}">
            <i data-lucide="list" class="w-3.5 h-3.5 flex-shrink-0"></i> Lacak Status
        </a>
    </div>
</div>
@endcan

{{-- VERIFIKASI (KASIUM) --}}
@can('pengajuan.verifikasi')
<a href="{{ route('kasium.verifikasi.index') }}"
    class="flex items-center gap-3 py-2.5 px-3 rounded-lg text-sm transition
          {{ request()->routeIs('kasium.verifikasi.*')
             ? 'bg-sipbo-gold text-sipbo-bg font-semibold'
             : 'text-sipbo-text-muted dark:text-light-text-muted hover:bg-sipbo-panel-light dark:hover:bg-light-panel-light hover:text-sipbo-text dark:hover:text-light-text' }}">
    <i data-lucide="check-square" class="w-4 h-4 flex-shrink-0"></i>
    <span>Verifikasi</span>
</a>
@endcan

{{-- PERSETUJUAN (Pimpinan) --}}
@can('pengajuan.setujui')
<a href="{{ route('pimpinan.approval.index') }}"
    class="flex items-center gap-3 py-2.5 px-3 rounded-lg text-sm transition
          {{ request()->routeIs('pimpinan.approval.*')
             ? 'bg-sipbo-gold text-sipbo-bg font-semibold'
             : 'text-sipbo-text-muted dark:text-light-text-muted hover:bg-sipbo-panel-light dark:hover:bg-light-panel-light hover:text-sipbo-text dark:hover:text-light-text' }}">
    <i data-lucide="stamp" class="w-4 h-4 flex-shrink-0"></i>
    <span>Persetujuan</span>
</a>
@endcan

{{-- BKU (KASIUM) --}}
@can('bku.lihat')
<a href="{{ route('kasium.bku.index') }}"
    class="flex items-center gap-3 py-2.5 px-3 rounded-lg text-sm transition
          {{ request()->routeIs('kasium.bku.*')
             ? 'bg-sipbo-gold text-sipbo-bg font-semibold'
             : 'text-sipbo-text-muted dark:text-light-text-muted hover:bg-sipbo-panel-light dark:hover:bg-light-panel-light hover:text-sipbo-text dark:hover:text-light-text' }}">
    <i data-lucide="wallet" class="w-4 h-4 flex-shrink-0"></i>
    <span>Pencatatan Realisasi</span>
</a>
@endcan

{{-- DIVIDER --}}
<div class="my-2 border-t border-sipbo-border dark:border-light-border opacity-70"></div>

{{-- LAPORAN --}}
@can('laporan.export-pdf')
<a href="{{ route('laporan.index') }}"
    class="flex items-center gap-3 py-2.5 px-3 rounded-lg text-sm transition
          {{ request()->routeIs('laporan.*')
             ? 'bg-sipbo-gold text-sipbo-bg font-semibold'
             : 'text-sipbo-text-muted dark:text-light-text-muted hover:bg-sipbo-panel-light dark:hover:bg-light-panel-light hover:text-sipbo-text dark:hover:text-light-text' }}">
    <i data-lucide="file-bar-chart" class="w-4 h-4 flex-shrink-0"></i>
    <span>Laporan Bulanan</span>
</a>
@endcan

{{-- ARSIP --}}
@can('arsip.lihat')
<a href="{{ route('arsip.index') }}"
    class="flex items-center gap-3 py-2.5 px-3 rounded-lg text-sm transition
          {{ request()->routeIs('arsip.*')
             ? 'bg-sipbo-gold text-sipbo-bg font-semibold'
             : 'text-sipbo-text-muted dark:text-light-text-muted hover:bg-sipbo-panel-light dark:hover:bg-light-panel-light hover:text-sipbo-text dark:hover:text-light-text' }}">
    <i data-lucide="archive" class="w-4 h-4 flex-shrink-0"></i>
    <span>Arsip Data</span>
</a>
@endcan

{{-- DIVIDER --}}
<div class="my-2 border-t border-sipbo-border dark:border-light-border opacity-70"></div>

{{-- SETTINGS --}}
@canany(['settings.users', 'settings.units', 'settings.permissions'])
<div x-data="{ open: {{ request()->routeIs('kasium.settings.*') ? 'true' : 'false' }} }">
    <button @click="open = !open"
        class="w-full flex items-center justify-between py-2.5 px-3 rounded-lg text-sm transition
               {{ request()->routeIs('kasium.settings.*')
                  ? 'text-sipbo-gold'
                  : 'text-sipbo-text-muted dark:text-light-text-muted hover:bg-sipbo-panel-light dark:hover:bg-light-panel-light hover:text-sipbo-text dark:hover:text-light-text' }}">
        <span class="flex items-center gap-3">
            <i data-lucide="settings-2" class="w-4 h-4 flex-shrink-0"></i>
            <span>Settings</span>
        </span>
        <i data-lucide="chevron-down"
            class="w-3.5 h-3.5 flex-shrink-0 transition-transform duration-200"
            :class="open ? 'rotate-180 text-sipbo-gold' : ''"></i>
    </button>

    <div x-show="open"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 -translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-1"
        class="mt-1 ml-4 pl-3 border-l border-sipbo-border dark:border-light-border space-y-0.5">

        <p class="text-[10px] text-sipbo-text-muted dark:text-light-text-muted
                  uppercase tracking-widest px-3 pt-1.5 pb-0.5 opacity-70">
            Master Data
        </p>

        @can('settings.units')
        <a href="{{ route('kasium.settings.units.index') }}"
            class="flex items-center gap-2 py-2 px-3 rounded-lg text-sm transition
                  {{ request()->routeIs('kasium.settings.units.*')
                     ? 'text-sipbo-gold font-medium bg-sipbo-gold/10'
                     : 'text-sipbo-text-muted dark:text-light-text-muted hover:text-sipbo-text dark:hover:text-light-text hover:bg-sipbo-panel-light dark:hover:bg-light-panel-light' }}">
            <i data-lucide="building-2" class="w-3.5 h-3.5 flex-shrink-0"></i> Units
        </a>
        @endcan

        @can('settings.users')
        <a href="{{ route('kasium.settings.users.index') }}"
            class="flex items-center gap-2 py-2 px-3 rounded-lg text-sm transition
                  {{ request()->routeIs('kasium.settings.users.*')
                     ? 'text-sipbo-gold font-medium bg-sipbo-gold/10'
                     : 'text-sipbo-text-muted dark:text-light-text-muted hover:text-sipbo-text dark:hover:text-light-text hover:bg-sipbo-panel-light dark:hover:bg-light-panel-light' }}">
            <i data-lucide="users" class="w-3.5 h-3.5 flex-shrink-0"></i> Users
        </a>
        @endcan

        @can('settings.permissions')
        <p class="text-[10px] text-sipbo-text-muted dark:text-light-text-muted
                  uppercase tracking-widest px-3 pt-2.5 pb-0.5 opacity-70">
            Hak Akses
        </p>

        <a href="{{ route('kasium.settings.permissions.role') }}"
            class="flex items-center gap-2 py-2 px-3 rounded-lg text-sm transition
                  {{ request()->routeIs('kasium.settings.permissions.role*')
                     ? 'text-sipbo-gold font-medium bg-sipbo-gold/10'
                     : 'text-sipbo-text-muted dark:text-light-text-muted hover:text-sipbo-text dark:hover:text-light-text hover:bg-sipbo-panel-light dark:hover:bg-light-panel-light' }}">
            <i data-lucide="shield-check" class="w-3.5 h-3.5 flex-shrink-0"></i> Per Role
        </a>

        <a href="{{ route('kasium.settings.permissions.user') }}"
            class="flex items-center gap-2 py-2 px-3 rounded-lg text-sm transition
                  {{ request()->routeIs('kasium.settings.permissions.user*')
                     ? 'text-sipbo-gold font-medium bg-sipbo-gold/10'
                     : 'text-sipbo-text-muted dark:text-light-text-muted hover:text-sipbo-text dark:hover:text-light-text hover:bg-sipbo-panel-light dark:hover:bg-light-panel-light' }}">
            <i data-lucide="user-cog" class="w-3.5 h-3.5 flex-shrink-0"></i> Per User
        </a>
        @endcan
    </div>
</div>
@endcanany