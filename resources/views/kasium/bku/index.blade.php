@extends('layouts.app')
@section('title', 'Buku Kas Umum')

@section('content')
<div class="space-y-4">

    <!-- FILTER + RINGKASAN -->
    <div class="bg-sipbo-panel dark:bg-light-panel
                border border-sipbo-border dark:border-light-border
                dark:shadow-sm rounded-2xl p-4 lg:p-5">

        <div class="flex flex-wrap justify-between items-center gap-4 mb-4">
            <form method="GET" class="flex items-center gap-2">
                <label class="text-sm text-sipbo-text-muted dark:text-light-text-muted">Program:</label>
                <select name="program_id" onchange="this.form.submit()"
                    class="bg-sipbo-bg dark:bg-light-panel-light
                           border border-sipbo-border dark:border-light-border
                           rounded-lg p-2 text-sm
                           text-sipbo-text dark:text-light-text
                           focus:ring-2 focus:ring-sipbo-gold">
                    @foreach($programs as $p)
                    <option value="{{ $p->id }}" {{ $programId == $p->id ? 'selected' : '' }}>
                        {{ $p->nama_program }} ({{ $p->kode_program }})
                    </option>
                    @endforeach
                </select>
            </form>

            @can('bku.input')
            <a href="{{ route('kasium.bku.create') }}"
                class="bg-sipbo-gold hover:bg-sipbo-gold-light text-sipbo-bg
                       px-4 py-2 rounded-lg text-sm font-semibold transition
                       flex items-center gap-2 active:scale-[0.98]">
                <i data-lucide="plus" class="w-4 h-4"></i> Catat Transaksi
            </a>
            @endcan
        </div>

        @if($program)
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-4
                    border-t border-sipbo-border dark:border-light-border">
            <div class="bg-sipbo-bg dark:bg-light-panel-light rounded-xl p-4">
                <p class="text-xs text-sipbo-text-muted dark:text-light-text-muted mb-1">Pagu DIPA</p>
                <p class="text-lg font-bold text-sipbo-text dark:text-light-text">
                    Rp {{ number_format($program->pagu_dipa, 0, ',', '.') }}
                </p>
            </div>
            <div class="bg-sipbo-bg dark:bg-light-panel-light rounded-xl p-4">
                <p class="text-xs text-sipbo-text-muted dark:text-light-text-muted mb-1">Total Realisasi (Kredit)</p>
                <p class="text-lg font-bold text-red-400 dark:text-red-600">
                    Rp {{ number_format($transaksi->sum('kredit'), 0, ',', '.') }}
                </p>
            </div>
            <div class="bg-sipbo-bg dark:bg-light-panel-light rounded-xl p-4">
                <p class="text-xs text-sipbo-text-muted dark:text-light-text-muted mb-1">Saldo Berjalan</p>
                <p class="text-lg font-bold text-sipbo-gold">
                    Rp {{ number_format($program->saldo_berjalan, 0, ',', '.') }}
                </p>
            </div>
        </div>
        @endif
    </div>

    <!-- TABEL TRANSAKSI -->
    <div class="bg-sipbo-panel dark:bg-light-panel
                border border-sipbo-border dark:border-light-border
                dark:shadow-sm rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs lg:text-sm">
                <thead>
                    <tr class="text-sipbo-text-muted dark:text-light-text-muted
                               border-b border-sipbo-border dark:border-light-border">
                        <th class="text-left p-3 font-medium">Tanggal</th>
                        <th class="text-left p-3 font-medium">Uraian</th>
                        <th class="text-left p-3 font-medium hidden lg:table-cell">Terkait Pengajuan</th>
                        <th class="text-right p-3 font-medium hidden md:table-cell">Debit</th>
                        <th class="text-right p-3 font-medium">Kredit</th>
                        <th class="text-right p-3 font-medium">Saldo</th>
                        <th class="text-left p-3 font-medium hidden lg:table-cell">Input oleh</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transaksi as $t)
                    <tr class="border-b border-sipbo-border/40 dark:border-light-border/60
                               hover:bg-sipbo-panel-light dark:hover:bg-light-panel-light transition">
                        <td class="p-3 text-sipbo-text-muted dark:text-light-text-muted">
                            {{ $t->tanggal_transaksi->format('d-m-Y') }}
                        </td>
                        <td class="p-3 text-sipbo-text dark:text-light-text">{{ $t->uraian }}</td>
                        <td class="p-3 text-sipbo-text-muted dark:text-light-text-muted hidden lg:table-cell">
                            {{ $t->pengajuanAnggaran?->judul_usulan ?? '-' }}
                        </td>
                        <td class="p-3 text-right text-green-500 dark:text-green-600 hidden md:table-cell">
                            {{ $t->debit > 0 ? number_format($t->debit, 0, ',', '.') : '-' }}
                        </td>
                        <td class="p-3 text-right text-red-400 dark:text-red-600">
                            {{ $t->kredit > 0 ? number_format($t->kredit, 0, ',', '.') : '-' }}
                        </td>
                        <td class="p-3 text-right font-semibold text-sipbo-gold">
                            {{ number_format($t->saldo, 0, ',', '.') }}
                        </td>
                        <td class="p-3 text-sipbo-text-muted dark:text-light-text-muted hidden lg:table-cell">
                            {{ $t->inputBy->name }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="p-8 text-center text-sipbo-text-muted dark:text-light-text-muted text-sm">
                            Belum ada transaksi untuk program ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-sipbo-border dark:border-light-border">
            {{ $transaksi->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection