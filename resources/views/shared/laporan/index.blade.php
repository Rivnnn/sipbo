@extends('layouts.app')
@section('title', 'Laporan Keuangan')

@section('content')
<div class="space-y-4 lg:space-y-5">

    <!-- FILTER PERIODE -->
    <div class="bg-sipbo-panel dark:bg-light-panel
                border border-sipbo-border dark:border-light-border
                dark:shadow-sm rounded-2xl p-4 lg:p-5">
        <form method="GET" class="flex flex-wrap items-end gap-3">
            <div>
                <label class="block text-xs text-sipbo-text-muted dark:text-light-text-muted mb-1.5">Bulan</label>
                <select name="bulan"
                    class="bg-sipbo-bg dark:bg-light-panel-light
                           border border-sipbo-border dark:border-light-border
                           rounded-xl p-2 text-sm text-sipbo-text dark:text-light-text
                           focus:ring-2 focus:ring-sipbo-gold">
                    @foreach(range(1,12) as $m)
                    <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::createFromDate(2000, $m, 1)->translatedFormat('F') }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs text-sipbo-text-muted dark:text-light-text-muted mb-1.5">Tahun</label>
                <select name="tahun"
                    class="bg-sipbo-bg dark:bg-light-panel-light
                           border border-sipbo-border dark:border-light-border
                           rounded-xl p-2 text-sm text-sipbo-text dark:text-light-text
                           focus:ring-2 focus:ring-sipbo-gold">
                    @foreach(range(now()->year, now()->year - 2) as $y)
                    <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <button class="bg-sipbo-gold hover:bg-sipbo-gold-light text-sipbo-bg
                           px-4 py-2 rounded-xl text-sm font-semibold transition">
                Terapkan Filter
            </button>
        </form>
    </div>

    <!-- REALISASI SEMUA PROGRAM -->
    <div class="bg-sipbo-panel dark:bg-light-panel
                border border-sipbo-border dark:border-light-border
                dark:shadow-sm rounded-2xl p-4 lg:p-5">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div>
                <h3 class="font-semibold text-sipbo-gold text-sm lg:text-base">
                    Laporan Realisasi Anggaran Semua Program
                </h3>
                <p class="text-xs text-sipbo-text-muted dark:text-light-text-muted mt-1">
                    Rekapitulasi realisasi seluruh program dalam satu periode.
                </p>
            </div>
            <div class="flex gap-2 flex-shrink-0">
                <a href="{{ route('laporan.realisasi-pdf', ['bulan' => $bulan, 'tahun' => $tahun]) }}"
                    class="bg-red-900/30 dark:bg-red-50 text-red-400 dark:text-red-700
                           border border-red-700/40 dark:border-red-300
                           px-3 py-2 rounded-xl text-xs font-medium
                           hover:bg-red-900/50 dark:hover:bg-red-100 transition">
                    📄 PDF
                </a>
                <a href="{{ route('laporan.realisasi-excel', ['bulan' => $bulan, 'tahun' => $tahun]) }}"
                    class="bg-green-900/30 dark:bg-green-50 text-green-400 dark:text-green-700
                           border border-green-700/40 dark:border-green-300
                           px-3 py-2 rounded-xl text-xs font-medium
                           hover:bg-green-900/50 dark:hover:bg-green-100 transition">
                    📊 Excel
                </a>
            </div>
        </div>
    </div>

    <!-- BKU PER PROGRAM -->
    <div class="bg-sipbo-panel dark:bg-light-panel
                border border-sipbo-border dark:border-light-border
                dark:shadow-sm rounded-2xl overflow-hidden">
        <div class="p-4 lg:p-5 border-b border-sipbo-border dark:border-light-border">
            <h3 class="font-semibold text-sipbo-gold text-sm lg:text-base">
                Laporan Buku Kas Umum per Program
            </h3>
            <p class="text-xs text-sipbo-text-muted dark:text-light-text-muted mt-1">
                Pilih program untuk mengunduh BKU periode terpilih.
            </p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-xs lg:text-sm">
                <thead>
                    <tr class="text-sipbo-text-muted dark:text-light-text-muted
                               border-b border-sipbo-border dark:border-light-border">
                        <th class="text-left p-3 font-medium">Kode</th>
                        <th class="text-left p-3 font-medium">Nama Program</th>
                        <th class="text-right p-3 font-medium hidden md:table-cell">Saldo Berjalan</th>
                        <th class="text-center p-3 font-medium">Unduh</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($programs as $p)
                    <tr class="border-b border-sipbo-border/40 dark:border-light-border/60
                               hover:bg-sipbo-panel-light dark:hover:bg-light-panel-light transition">
                        <td class="p-3">
                            <span class="bg-sipbo-panel-light dark:bg-light-panel-light
                                         text-sipbo-text-muted dark:text-light-text-muted
                                         px-2 py-0.5 rounded-full text-[10px]">
                                {{ $p->kode_program }}
                            </span>
                        </td>
                        <td class="p-3 text-sipbo-text dark:text-light-text">{{ $p->nama_program }}</td>
                        <td class="p-3 text-right text-sipbo-text dark:text-light-text hidden md:table-cell">
                            Rp {{ number_format($p->saldo_berjalan, 0, ',', '.') }}
                        </td>
                        <td class="p-3 text-center space-x-2">
                            <a href="{{ route('laporan.pdf',   ['program' => $p->id, 'bulan' => $bulan, 'tahun' => $tahun]) }}"
                                class="text-red-400 dark:text-red-600 hover:underline">PDF</a>
                            <span class="text-sipbo-border dark:text-light-border">|</span>
                            <a href="{{ route('laporan.excel', ['program' => $p->id, 'bulan' => $bulan, 'tahun' => $tahun]) }}"
                                class="text-green-400 dark:text-green-600 hover:underline">Excel</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection