@extends('layouts.app')
@section('title', 'Settings - Unit Kerja')

@section('content')
<div class="bg-sipbo-panel dark:bg-light-panel
            border border-sipbo-border dark:border-light-border
            dark:shadow-sm rounded-2xl overflow-hidden">

    <div class="p-4 lg:p-5 border-b border-sipbo-border dark:border-light-border
                flex justify-between items-center gap-3">
        <div>
            <h2 class="font-semibold text-sipbo-gold text-sm lg:text-base">Daftar Unit Kerja</h2>
            <p class="text-xs text-sipbo-text-muted dark:text-light-text-muted mt-1">
                Kelola unit kerja di lingkungan Polsek.
            </p>
        </div>
        <a href="{{ route('kasium.settings.units.create') }}"
            class="bg-sipbo-gold hover:bg-sipbo-gold-light text-sipbo-bg
                   px-3 lg:px-4 py-2 rounded-xl text-xs lg:text-sm font-semibold transition
                   flex items-center gap-2 flex-shrink-0">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span class="hidden sm:inline">Tambah Unit</span>
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-xs lg:text-sm">
            <thead>
                <tr class="text-sipbo-text-muted dark:text-light-text-muted
                           border-b border-sipbo-border dark:border-light-border">
                    <th class="text-left p-3 font-medium">Kode</th>
                    <th class="text-left p-3 font-medium">Nama Unit</th>
                    <th class="text-center p-3 font-medium hidden md:table-cell">Jumlah User</th>
                    <th class="text-center p-3 font-medium hidden md:table-cell">Jumlah Pengajuan</th>
                    <th class="text-left p-3 font-medium">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($units as $u)
                <tr class="border-b border-sipbo-border/40 dark:border-light-border/60
                           hover:bg-sipbo-panel-light dark:hover:bg-light-panel-light transition">
                    <td class="p-3">
                        <span class="bg-sipbo-panel-light dark:bg-light-panel-light
                                     text-sipbo-text-muted dark:text-light-text-muted
                                     px-2 py-0.5 rounded-full text-[10px]">
                            {{ $u->kode_unit }}
                        </span>
                    </td>
                    <td class="p-3 font-medium text-sipbo-text dark:text-light-text">{{ $u->nama_unit }}</td>
                    <td class="p-3 text-center text-sipbo-text-muted dark:text-light-text-muted hidden md:table-cell">
                        {{ $u->users_count }}
                    </td>
                    <td class="p-3 text-center text-sipbo-text-muted dark:text-light-text-muted hidden md:table-cell">
                        {{ $u->pengajuans_count }}
                    </td>
                    <td class="p-3">
                        <div class="flex gap-3">
                            <a href="{{ route('kasium.settings.units.edit', $u->id) }}"
                                class="text-sipbo-gold hover:underline">Edit</a>
                            <form action="{{ route('kasium.settings.units.destroy', $u->id) }}" method="POST"
                                onsubmit="return confirm('Hapus unit kerja ini?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="text-red-400 dark:text-red-600 hover:underline">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="p-8 text-center text-sm
                                           text-sipbo-text-muted dark:text-light-text-muted">
                        Belum ada unit kerja.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-4 border-t border-sipbo-border dark:border-light-border">
        {{ $units->links() }}
    </div>
</div>
@endsection