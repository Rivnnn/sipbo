@extends('layouts.app')
@section('title', 'Verifikasi Pengajuan')

@section('content')
<div class="bg-sipbo-panel dark:bg-light-panel
            border border-sipbo-border dark:border-light-border
            dark:shadow-sm rounded-2xl overflow-hidden">

    <div class="p-4 lg:p-5 border-b border-sipbo-border dark:border-light-border">
        <h2 class="font-semibold text-sipbo-gold text-sm lg:text-base">Daftar Pengajuan untuk Diproses</h2>
        <p class="text-xs text-sipbo-text-muted dark:text-light-text-muted mt-1">
            Verifikasi, ajukan ke Polrestabes, atau tandai dana cair.
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
                    <th class="text-left p-3 font-medium">Status</th>
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
                    <td class="p-3">
                        @php [$class, $label] = $p->statusBadge(); @endphp
                        <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-medium {{ $class }}">
                            {{ $label }}
                        </span>
                    </td>
                    <td class="p-3">
                        @if($p->status === 'menunggu_verifikasi')
                        <div class="flex flex-wrap gap-2">
                            <form action="{{ route('kasium.verifikasi.proses', $p->id) }}" method="POST">
                                @csrf
                                <button class="bg-sipbo-gold hover:bg-sipbo-gold-light text-sipbo-bg
                                               px-3 py-1.5 rounded-lg text-xs font-semibold transition">
                                    Verifikasi
                                </button>
                            </form>
                            <form action="{{ route('kasium.verifikasi.tolak', $p->id) }}" method="POST"
                                onsubmit="return confirm('Tolak pengajuan ini?')">
                                @csrf
                                <input type="hidden" name="catatan" value="Tidak lengkap secara administratif">
                                <button class="border border-red-500/50 text-red-400 dark:text-red-600
                                               dark:border-red-400/50 px-3 py-1.5 rounded-lg text-xs
                                               font-medium hover:bg-red-900/20 dark:hover:bg-red-50 transition">
                                    Tolak
                                </button>
                            </form>
                        </div>

                        @elseif($p->status === 'disetujui_pimpinan')
                        <form action="{{ route('kasium.eksternal.ajukan', $p->id) }}" method="POST"
                            class="flex flex-wrap gap-2">
                            @csrf
                            <input type="text" name="nomor_referensi_astina" required
                                placeholder="No. Ref ASTINA"
                                class="bg-sipbo-bg dark:bg-light-panel-light
                                          border border-sipbo-border dark:border-light-border
                                          rounded-lg px-2 py-1 text-xs w-28 lg:w-32
                                          text-sipbo-text dark:text-light-text
                                          focus:ring-2 focus:ring-sipbo-gold">
                            <button class="bg-indigo-900/40 dark:bg-indigo-100 text-indigo-400 dark:text-indigo-700
                                           border border-indigo-700/50 dark:border-indigo-300
                                           px-3 py-1.5 rounded-lg text-xs font-medium
                                           hover:bg-indigo-900/60 dark:hover:bg-indigo-200 transition">
                                Ajukan Polrestabes
                            </button>
                        </form>

                        @elseif($p->status === 'diajukan_ke_polrestabes')
                        <form action="{{ route('kasium.eksternal.dana-cair', $p->id) }}" method="POST"
                            onsubmit="return confirm('Tandai dana sudah cair?')">
                            @csrf
                            <button class="bg-green-900/40 dark:bg-green-100
                                           text-green-400 dark:text-green-700
                                           border border-green-700/50 dark:border-green-300
                                           px-3 py-1.5 rounded-lg text-xs font-medium
                                           hover:bg-green-900/60 dark:hover:bg-green-200 transition">
                                Tandai Dana Cair
                            </button>
                        </form>

                        @else
                        <span class="text-sipbo-text-muted dark:text-light-text-muted text-xs">
                            Menunggu pimpinan
                        </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="p-8 text-center text-sm
                                           text-sipbo-text-muted dark:text-light-text-muted">
                        Tidak ada pengajuan yang perlu diproses.
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