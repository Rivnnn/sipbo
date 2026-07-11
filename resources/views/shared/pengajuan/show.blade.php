@extends('layouts.app')
@section('title', 'Detail Pengajuan')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-5">

    <div class="lg:col-span-2 space-y-4">

        <!-- INFO UTAMA -->
        <div class="bg-sipbo-panel dark:bg-light-panel
                    border border-sipbo-border dark:border-light-border
                    dark:shadow-sm rounded-2xl p-4 lg:p-6">
            <div class="flex flex-col sm:flex-row justify-between items-start gap-3 mb-4">
                <div>
                    <h2 class="text-base lg:text-lg font-semibold
                               text-sipbo-text dark:text-light-text">
                        {{ $pengajuan->judul_usulan }}
                    </h2>
                    <p class="text-xs text-sipbo-text-muted dark:text-light-text-muted mt-1">
                        {{ $pengajuan->unitKerja->nama_unit }}
                        &nbsp;•&nbsp;
                        {{ $pengajuan->programAnggaran->nama_program }}
                    </p>
                </div>
                @php [$class, $label] = $pengajuan->statusBadge(); @endphp
                <span class="px-3 py-1.5 rounded-full text-xs font-medium {{ $class }} flex-shrink-0">
                    {{ $label }}
                </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm
                        border-t border-sipbo-border dark:border-light-border pt-4">
                <div>
                    <p class="text-sipbo-text-muted dark:text-light-text-muted text-xs mb-1">Nominal Usulan</p>
                    <p class="font-bold text-xl text-sipbo-gold">
                        Rp {{ number_format($pengajuan->nominal_usulan, 0, ',', '.') }}
                    </p>
                </div>
                <div>
                    <p class="text-sipbo-text-muted dark:text-light-text-muted text-xs mb-1">Diajukan oleh</p>
                    <p class="font-medium text-sipbo-text dark:text-light-text">{{ $pengajuan->user->name }}</p>
                </div>
                @if($pengajuan->keterangan)
                <div class="sm:col-span-2">
                    <p class="text-sipbo-text-muted dark:text-light-text-muted text-xs mb-1">Keterangan</p>
                    <p class="text-sipbo-text dark:text-light-text">{{ $pengajuan->keterangan }}</p>
                </div>
                @endif
                @if($pengajuan->nomor_referensi_astina)
                <div>
                    <p class="text-sipbo-text-muted dark:text-light-text-muted text-xs mb-1">No. Referensi ASTINA</p>
                    <p class="font-medium text-sipbo-text dark:text-light-text">
                        {{ $pengajuan->nomor_referensi_astina }}
                    </p>
                </div>
                @endif
                @if($pengajuan->file_lampiran)
                <div>
                    <p class="text-sipbo-text-muted dark:text-light-text-muted text-xs mb-1">Lampiran</p>
                    <a href="{{ Storage::url($pengajuan->file_lampiran) }}" target="_blank"
                        class="text-sipbo-gold hover:underline text-sm">Lihat File</a>
                </div>
                @endif
            </div>
        </div>

        {{-- AKSI: STAF UNIT --}}
        @can('pengajuan.kirim')
        @if($pengajuan->status === 'draft' && $pengajuan->user_id === auth()->id())
        <div class="bg-sipbo-panel dark:bg-light-panel
                    border border-sipbo-border dark:border-light-border
                    dark:shadow-sm rounded-2xl p-4 flex flex-wrap gap-3">
            <a href="{{ route('staf.pengajuan.edit', $pengajuan->id) }}"
                class="border border-sipbo-border dark:border-light-border
                       text-sipbo-text-muted dark:text-light-text-muted
                       px-4 py-2 rounded-xl text-sm
                       hover:bg-sipbo-panel-light dark:hover:bg-light-panel-light transition">
                Edit
            </a>
            <form action="{{ route('staf.pengajuan.kirim', $pengajuan->id) }}" method="POST">
                @csrf
                <button class="bg-sipbo-gold hover:bg-sipbo-gold-light text-sipbo-bg
                               px-4 py-2 rounded-xl text-sm font-semibold transition">
                    Kirim untuk Verifikasi
                </button>
            </form>
        </div>
        @endif
        @endcan

        {{-- AKSI: KASIUM --}}
        @can('pengajuan.verifikasi')
        @if($pengajuan->status === 'menunggu_verifikasi')
        <div class="bg-sipbo-panel dark:bg-light-panel
                    border border-sipbo-border dark:border-light-border
                    dark:shadow-sm rounded-2xl p-4 space-y-3">
            <form action="{{ route('kasium.verifikasi.proses', $pengajuan->id) }}" method="POST"
                class="space-y-3">
                @csrf
                <textarea name="catatan" rows="2" placeholder="Catatan verifikasi (opsional)"
                    class="w-full bg-sipbo-bg dark:bg-light-panel-light
                           border border-sipbo-border dark:border-light-border
                           rounded-xl p-2.5 text-sm text-sipbo-text dark:text-light-text
                           placeholder-sipbo-text-muted focus:ring-2 focus:ring-sipbo-gold"></textarea>
                <button class="bg-sipbo-gold hover:bg-sipbo-gold-light text-sipbo-bg
                               px-4 py-2 rounded-xl text-sm font-semibold transition">
                    Verifikasi
                </button>
            </form>
            <form id="form-tolak-verifikasi" action="{{ route('kasium.verifikasi.tolak', $pengajuan->id) }}" method="POST" class="hidden">
                @csrf
                <input type="hidden" name="catatan" value="Tidak lengkap administratif">
            </form>
            <button type="button"
                onclick="confirmAction({message: 'Tolak pengajuan ini?', formId: 'form-tolak-verifikasi', danger: true, confirmLabel: 'Ya, Tolak'})" class="border border-red-500/50 dark:border-red-400/50
                               text-red-400 dark:text-red-600
                               px-4 py-2 rounded-xl text-sm font-medium
                               hover:bg-red-900/20 dark:hover:bg-red-50 transition">
                    Tolak
                </button>
        </div>
        @endif

        @if($pengajuan->status === 'disetujui_pimpinan')
        <div class="bg-sipbo-panel dark:bg-light-panel
                    border border-sipbo-border dark:border-light-border
                    dark:shadow-sm rounded-2xl p-4">
            <form action="{{ route('kasium.eksternal.ajukan', $pengajuan->id) }}" method="POST"
                class="space-y-3">
                @csrf
                <label class="block text-sm text-sipbo-text dark:text-light-text">No. Referensi ASTINA</label>
                <input type="text" name="nomor_referensi_astina" required
                    placeholder="Contoh: ASTINA/2026/06/001"
                    class="w-full bg-sipbo-bg dark:bg-light-panel-light
                           border border-sipbo-border dark:border-light-border
                           rounded-xl p-2.5 text-sm text-sipbo-text dark:text-light-text
                           placeholder-sipbo-text-muted focus:ring-2 focus:ring-sipbo-gold">
                <button class="bg-sipbo-gold hover:bg-sipbo-gold-light text-sipbo-bg
                               px-4 py-2 rounded-xl text-sm font-semibold transition">
                    Tandai Diajukan ke Polrestabes
                </button>
            </form>
        </div>
        @endif

        @if($pengajuan->status === 'diajukan_ke_polrestabes')
        <div class="bg-sipbo-panel dark:bg-light-panel
                    border border-sipbo-border dark:border-light-border
                    dark:shadow-sm rounded-2xl p-4">
            <form action="{{ route('kasium.eksternal.dana-cair', $pengajuan->id) }}" method="POST">
                @csrf
                <button class="bg-green-900/40 dark:bg-green-100
                               text-green-400 dark:text-green-700
                               border border-green-700/50 dark:border-green-300
                               px-4 py-2 rounded-xl text-sm font-medium
                               hover:bg-green-900/60 dark:hover:bg-green-200 transition">
                    Tandai Dana Cair → Input BKU
                </button>
            </form>
        </div>
        @endif
        @endcan

        {{-- AKSI: PIMPINAN --}}
        @can('pengajuan.setujui')
        @if($pengajuan->status === 'terverifikasi')
        <div class="bg-sipbo-panel dark:bg-light-panel
                    border border-sipbo-border dark:border-light-border
                    dark:shadow-sm rounded-2xl p-4 space-y-3">
            <form action="{{ route('pimpinan.approval.setujui', $pengajuan->id) }}" method="POST"
                class="space-y-3">
                @csrf
                <textarea name="catatan" rows="2" placeholder="Catatan persetujuan (opsional)"
                    class="w-full bg-sipbo-bg dark:bg-light-panel-light
                           border border-sipbo-border dark:border-light-border
                           rounded-xl p-2.5 text-sm text-sipbo-text dark:text-light-text
                           placeholder-sipbo-text-muted focus:ring-2 focus:ring-sipbo-gold"></textarea>
                <button class="bg-sipbo-gold hover:bg-sipbo-gold-light text-sipbo-bg
                               px-4 py-2 rounded-xl text-sm font-semibold transition">
                    ✓ Setujui (ACC)
                </button>
            </form>
            <form id="form-tolak-approval" action="{{ route('pimpinan.approval.tolak', $pengajuan->id) }}" method="POST" class="hidden">
                @csrf
                <input type="hidden" name="catatan" value="Ditolak oleh Pimpinan">
            </form>
            <button type="button"
                onclick="confirmAction({message: 'Tolak pengajuan ini?', formId: 'form-tolak-approval', danger: true, confirmLabel: 'Ya, Tolak'})"
                class="border border-red-500/50 dark:border-red-400/50
                               text-red-400 dark:text-red-600
                               px-4 py-2 rounded-xl text-sm font-medium
                               hover:bg-red-900/20 dark:hover:bg-red-50 transition">
                    Tolak
                </button>
        </div>
        @endif
        @endcan
    </div>

    {{-- TIMELINE / AUDIT TRAIL --}}
    <div class="bg-sipbo-panel dark:bg-light-panel
                border border-sipbo-border dark:border-light-border
                dark:shadow-sm rounded-2xl p-4 lg:p-5">
        <h3 class="font-semibold text-sipbo-gold text-sm mb-4">Riwayat / Audit Trail</h3>
        <div class="space-y-1">
            @forelse($pengajuan->auditTrails as $trail)
            <div class="flex gap-3">
                <div class="flex flex-col items-center">
                    <div class="w-2.5 h-2.5 rounded-full bg-sipbo-gold mt-1 flex-shrink-0"></div>
                    @if(!$loop->last)
                    <div class="w-px flex-1 bg-sipbo-border dark:bg-light-border my-1"></div>
                    @endif
                </div>
                <div class="pb-4">
                    <p class="text-sm font-medium text-sipbo-text dark:text-light-text">
                        {{ ucfirst(str_replace('_', ' ', $trail->status_sesudah)) }}
                    </p>
                    <p class="text-xs text-sipbo-text-muted dark:text-light-text-muted">
                        {{ $trail->waktu_eksekusi->format('d M Y, H:i') }} WIB
                    </p>
                    <p class="text-xs text-sipbo-text-muted dark:text-light-text-muted">
                        oleh {{ $trail->actor->name }}
                    </p>
                    @if($trail->catatan)
                    <p class="text-xs text-sipbo-text-muted dark:text-light-text-muted mt-1 italic">
                        "{{ $trail->catatan }}"
                    </p>
                    @endif
                </div>
            </div>
            @empty
            <p class="text-sm text-sipbo-text-muted dark:text-light-text-muted">Belum ada riwayat.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection