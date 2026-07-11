@extends('layouts.app')
@section('title', 'Persetujuan Anggaran')

@section('content')
<div class="bg-sipbo-panel dark:bg-light-panel
            border border-sipbo-border dark:border-light-border
            dark:shadow-sm rounded-2xl overflow-hidden">

    <div class="p-4 lg:p-5 border-b border-sipbo-border dark:border-light-border">
        <h2 class="font-semibold text-sipbo-gold text-sm lg:text-base">Pengajuan Menunggu Persetujuan</h2>
        <p class="text-xs text-sipbo-text-muted dark:text-light-text-muted mt-1">
            Pengajuan yang telah diverifikasi KASIUM dan menunggu ACC Anda.
        </p>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-xs lg:text-sm">
            <thead>
                <tr class="text-sipbo-text-muted dark:text-light-text-muted
                           border-b border-sipbo-border dark:border-light-border">
                    <th class="text-left p-3 font-medium">Unit</th>
                    <th class="text-left p-3 font-medium">Judul</th>
                    <th class="text-right p-3 font-medium hidden md:table-cell">Nominal</th>
                    <th class="text-left p-3 font-medium hidden lg:table-cell">Diverifikasi oleh</th>
                    <th class="text-left p-3 font-medium">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pengajuans as $p)
                <tr class="border-b border-sipbo-border/40 dark:border-light-border/60
                           hover:bg-sipbo-panel-light dark:hover:bg-light-panel-light transition">
                    <td class="p-3 text-sipbo-text-muted dark:text-light-text-muted">
                        {{ $p->unitKerja->kode_unit }}
                    </td>
                    <td class="p-3 font-medium text-sipbo-text dark:text-light-text">
                        <a href="{{ route('pengajuan.show', $p->id) }}"
                            class="hover:text-sipbo-gold transition">{{ $p->judul_usulan }}</a>
                    </td>
                    <td class="p-3 text-right text-sipbo-text dark:text-light-text hidden md:table-cell">
                        Rp {{ number_format($p->nominal_usulan, 0, ',', '.') }}
                    </td>
                    <td class="p-3 text-sipbo-text-muted dark:text-light-text-muted hidden lg:table-cell">
                        {{ $p->verifier->name ?? '-' }}
                    </td>
                    <td class="p-3">
                        <div class="flex flex-wrap gap-2">
                            <form id="form-setujui-{{ $p->id }}" action="{{ route('pimpinan.approval.setujui', $p->id) }}" method="POST" class="hidden">
                                @csrf
                            </form>
                            <button type="button"
                                onclick="confirmAction({message: 'Setujui pengajuan {{ addslashes($p->judul_usulan) }}?', formId: 'form-setujui-{{ $p->id }}', confirmLabel: 'Ya, Setujui'})"
                                class="bg-sipbo-gold hover:bg-sipbo-gold-light text-sipbo-bg
                                               px-3 py-1.5 rounded-lg text-xs font-semibold transition">
                                ✓ Setujui
                            </button>
                            <form id="form-tolak-{{ $p->id }}" action="{{ route('pimpinan.approval.tolak', $p->id) }}" method="POST" class="hidden">
                                @csrf
                                <input type="hidden" name="catatan" value="Ditolak oleh Pimpinan">
                            </form>
                            <button type="button"
                                onclick="confirmAction({message: 'Tolak pengajuan {{ addslashes($p->judul_usulan) }}?', formId: 'form-tolak-{{ $p->id }}', danger: true, confirmLabel: 'Ya, Tolak'})"
                                class="border border-red-500/50 dark:border-red-400/50
                                               text-red-400 dark:text-red-600
                                               px-3 py-1.5 rounded-lg text-xs font-medium
                                               hover:bg-red-900/20 dark:hover:bg-red-50 transition">
                                Tolak
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="p-8 text-center text-sm
                                           text-sipbo-text-muted dark:text-light-text-muted">
                        Tidak ada pengajuan yang menunggu persetujuan.
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