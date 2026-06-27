@extends('layouts.app')
@section('title', 'Pengajuan Anggaran Saya')

@section('content')
<div class="bg-sipbo-panel dark:bg-light-panel
            border border-sipbo-border dark:border-light-border
            dark:shadow-sm rounded-2xl overflow-hidden">

    <div class="p-4 lg:p-5 border-b border-sipbo-border dark:border-light-border
                flex justify-between items-center gap-3">
        <div>
            <h2 class="font-semibold text-sipbo-gold text-sm lg:text-base">Daftar Pengajuan Anggaran</h2>
            <p class="text-xs text-sipbo-text-muted dark:text-light-text-muted mt-1">
                Semua pengajuan yang Anda buat.
            </p>
        </div>
        <a href="{{ route('staf.pengajuan.create') }}"
            class="bg-sipbo-gold hover:bg-sipbo-gold-light text-sipbo-bg
                   px-3 lg:px-4 py-2 rounded-xl text-xs lg:text-sm font-semibold transition
                   flex items-center gap-2 flex-shrink-0">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span class="hidden sm:inline">Pengajuan Baru</span>
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-xs lg:text-sm">
            <thead>
                <tr class="text-sipbo-text-muted dark:text-light-text-muted
                           border-b border-sipbo-border dark:border-light-border">
                    <th class="text-left p-3 font-medium">Judul</th>
                    <th class="text-left p-3 font-medium hidden md:table-cell">Program</th>
                    <th class="text-right p-3 font-medium">Nominal</th>
                    <th class="text-left p-3 font-medium">Status</th>
                    <th class="text-left p-3 font-medium hidden lg:table-cell">Diperbarui</th>
                    <th class="text-left p-3 font-medium">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pengajuans as $p)
                <tr class="border-b border-sipbo-border/40 dark:border-light-border/60
                           hover:bg-sipbo-panel-light dark:hover:bg-light-panel-light transition">
                    <td class="p-3 font-medium text-sipbo-text dark:text-light-text">
                        {{ $p->judul_usulan }}
                    </td>
                    <td class="p-3 text-sipbo-text-muted dark:text-light-text-muted hidden md:table-cell">
                        {{ $p->programAnggaran->nama_program }}
                    </td>
                    <td class="p-3 text-right text-sipbo-text dark:text-light-text">
                        Rp {{ number_format($p->nominal_usulan, 0, ',', '.') }}
                    </td>
                    <td class="p-3">
                        @php [$class, $label] = $p->statusBadge(); @endphp
                        <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-medium {{ $class }}">
                            {{ $label }}
                        </span>
                    </td>
                    <td class="p-3 text-sipbo-text-muted dark:text-light-text-muted text-xs hidden lg:table-cell">
                        {{ $p->updated_at->diffForHumans() }}
                    </td>
                    <td class="p-3 space-x-2">
                        @if($p->status === 'draft')
                        <a href="{{ route('staf.pengajuan.edit', $p->id) }}"
                            class="text-sipbo-gold hover:underline">Edit</a>
                        <span class="text-sipbo-border dark:text-light-border">|</span>
                        @endif
                        <a href="{{ route('pengajuan.show', $p->id) }}"
                            class="text-sipbo-text-muted dark:text-light-text-muted
                                  hover:text-sipbo-text dark:hover:text-light-text hover:underline">
                            Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="p-8 text-center text-sm
                                           text-sipbo-text-muted dark:text-light-text-muted">
                        Belum ada pengajuan. Klik "Pengajuan Baru" untuk membuat.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-4 border-t border-sipbo-border dark:border-light-border">
        {{ $pengajuans->links() }}
    </div>
</div>
@endsection