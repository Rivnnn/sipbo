@extends('layouts.app')
@section('title', 'Arsip Data Pengajuan')

@section('content')
<div class="space-y-4">

    <!-- FILTER -->
    <div class="bg-sipbo-panel dark:bg-light-panel
                border border-sipbo-border dark:border-light-border
                dark:shadow-sm rounded-2xl p-4 lg:p-5">
        <form method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-3">

            <div class="sm:col-span-2">
                <label class="block text-xs text-sipbo-text-muted dark:text-light-text-muted mb-1.5">
                    Cari Judul Usulan
                </label>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Cari berdasarkan judul..."
                    class="w-full bg-sipbo-bg dark:bg-light-panel-light
                           border border-sipbo-border dark:border-light-border
                           rounded-xl p-2.5 text-sm text-sipbo-text dark:text-light-text
                           placeholder-sipbo-text-muted focus:ring-2 focus:ring-sipbo-gold">
            </div>

            <div>
                <label class="block text-xs text-sipbo-text-muted dark:text-light-text-muted mb-1.5">Status</label>
                <select name="status"
                    class="w-full bg-sipbo-bg dark:bg-light-panel-light
                           border border-sipbo-border dark:border-light-border
                           rounded-xl p-2.5 text-sm text-sipbo-text dark:text-light-text
                           focus:ring-2 focus:ring-sipbo-gold">
                    <option value="">Semua</option>
                    <option value="dana_cair" {{ request('status') == 'dana_cair' ? 'selected' : '' }}>Dana Cair</option>
                    <option value="ditolak" {{ request('status') == 'ditolak'   ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>

            <div>
                <label class="block text-xs text-sipbo-text-muted dark:text-light-text-muted mb-1.5">Tahun</label>
                <select name="tahun"
                    class="w-full bg-sipbo-bg dark:bg-light-panel-light
                           border border-sipbo-border dark:border-light-border
                           rounded-xl p-2.5 text-sm text-sipbo-text dark:text-light-text
                           focus:ring-2 focus:ring-sipbo-gold">
                    <option value="">Semua</option>
                    @foreach(range(now()->year, now()->year - 3) as $y)
                    <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-4 flex gap-2">
                <button type="submit"
                    class="bg-sipbo-gold hover:bg-sipbo-gold-light text-sipbo-bg
                           px-4 py-2 rounded-xl text-sm font-semibold transition">
                    Cari
                </button>
                <a href="{{ route('arsip.index') }}"
                    class="border border-sipbo-border dark:border-light-border
                           text-sipbo-text-muted dark:text-light-text-muted
                           px-4 py-2 rounded-xl text-sm
                           hover:bg-sipbo-panel-light dark:hover:bg-light-panel-light transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- TABLE -->
    <div class="bg-sipbo-panel dark:bg-light-panel
                border border-sipbo-border dark:border-light-border
                dark:shadow-sm rounded-2xl overflow-hidden">

        <div class="p-4 lg:p-5 border-b border-sipbo-border dark:border-light-border">
            <h2 class="font-semibold text-sipbo-gold text-sm lg:text-base">Riwayat Pengajuan Selesai</h2>
            <p class="text-xs text-sipbo-text-muted dark:text-light-text-muted mt-1">
                Pengajuan dengan status Dana Cair atau Ditolak.
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs lg:text-sm">
                <thead>
                    <tr class="text-sipbo-text-muted dark:text-light-text-muted
                               border-b border-sipbo-border dark:border-light-border">
                        <th class="text-left p-3 font-medium">Judul</th>
                        <th class="text-left p-3 font-medium">Unit</th>
                        <th class="text-left p-3 font-medium hidden md:table-cell">Program</th>
                        <th class="text-right p-3 font-medium">Nominal</th>
                        <th class="text-left p-3 font-medium">Status</th>
                        <th class="text-left p-3 font-medium hidden md:table-cell">Tgl Selesai</th>
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
                        <td class="p-3 text-sipbo-text-muted dark:text-light-text-muted">
                            {{ $p->unitKerja->kode_unit }}
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
                        <td class="p-3 text-sipbo-text-muted dark:text-light-text-muted hidden md:table-cell">
                            {{ ($p->dana_cair_pada ?? $p->updated_at)->format('d-m-Y') }}
                        </td>
                        <td class="p-3">
                            <a href="{{ route('pengajuan.show', $p->id) }}"
                                class="text-sipbo-gold hover:underline text-xs">Detail</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="p-8 text-center text-sm
                                               text-sipbo-text-muted dark:text-light-text-muted">
                            Tidak ada data ditemukan.
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
</div>
@endsection