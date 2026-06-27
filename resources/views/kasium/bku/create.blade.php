@extends('layouts.app')
@section('title', 'Catat Transaksi Kas')

@section('content')
<div class="bg-sipbo-panel dark:bg-light-panel
            border border-sipbo-border dark:border-light-border
            dark:shadow-sm rounded-2xl p-4 lg:p-6 max-w-lg">

    @if($pengajuan)
    <div class="bg-sipbo-gold/10 border border-sipbo-gold/30 rounded-xl p-3 mb-5 text-sm">
        <p class="text-sipbo-text-muted dark:text-light-text-muted text-xs mb-0.5">Transaksi terkait pengajuan:</p>
        <p class="font-semibold text-sipbo-gold">{{ $pengajuan->judul_usulan }}</p>
        <p class="text-sipbo-text-muted dark:text-light-text-muted text-xs mt-0.5">
            Rp {{ number_format($pengajuan->nominal_usulan, 0, ',', '.') }}
        </p>
    </div>
    @endif

    @if($errors->any())
    <div class="flash-error rounded-xl p-3 mb-4 text-sm">
        @foreach($errors->all() as $error)
        <p>{{ $error }}</p>
        @endforeach
    </div>
    @endif

    <form action="{{ route('kasium.bku.store') }}" method="POST" class="space-y-4">
        @csrf

        @if($pengajuan)
        <input type="hidden" name="pengajuan_anggaran_id" value="{{ $pengajuan->id }}">
        <input type="hidden" name="program_anggaran_id" value="{{ $pengajuan->program_anggaran_id }}">
        <div>
            <label class="block text-sm text-sipbo-text dark:text-light-text mb-1.5">Program</label>
            <input type="text" value="{{ $pengajuan->programAnggaran->nama_program }}" disabled
                class="w-full bg-sipbo-bg dark:bg-light-panel-light
                       border border-sipbo-border dark:border-light-border
                       rounded-xl p-2.5 text-sm text-sipbo-text-muted dark:text-light-text-muted
                       cursor-not-allowed">
        </div>
        @else
        <div>
            <label class="block text-sm text-sipbo-text dark:text-light-text mb-1.5">Program Anggaran</label>
            <select name="program_anggaran_id" required
                class="w-full bg-sipbo-bg dark:bg-light-panel-light
                       border border-sipbo-border dark:border-light-border
                       rounded-xl p-2.5 text-sm text-sipbo-text dark:text-light-text
                       focus:ring-2 focus:ring-sipbo-gold">
                <option value="">-- Pilih Program --</option>
                @foreach($programs as $p)
                <option value="{{ $p->id }}">
                    {{ $p->nama_program }} (Saldo: Rp {{ number_format($p->saldo_berjalan, 0, ',', '.') }})
                </option>
                @endforeach
            </select>
        </div>
        @endif

        <div>
            <label class="block text-sm text-sipbo-text dark:text-light-text mb-1.5">Tanggal Transaksi</label>
            <input type="date" name="tanggal_transaksi" value="{{ now()->format('Y-m-d') }}" required
                class="w-full bg-sipbo-bg dark:bg-light-panel-light
                       border border-sipbo-border dark:border-light-border
                       rounded-xl p-2.5 text-sm text-sipbo-text dark:text-light-text
                       focus:ring-2 focus:ring-sipbo-gold">
        </div>

        <div>
            <label class="block text-sm text-sipbo-text dark:text-light-text mb-1.5">Uraian</label>
            <input type="text" name="uraian" required
                value="{{ $pengajuan ? 'Distribusi dana - ' . $pengajuan->judul_usulan : '' }}"
                placeholder="Contoh: Distribusi dana ke Unit Lalu Lintas"
                class="w-full bg-sipbo-bg dark:bg-light-panel-light
                       border border-sipbo-border dark:border-light-border
                       rounded-xl p-2.5 text-sm text-sipbo-text dark:text-light-text
                       placeholder-sipbo-text-muted focus:ring-2 focus:ring-sipbo-gold">
        </div>

        <div>
            <label class="block text-sm text-sipbo-text dark:text-light-text mb-1.5">Nominal Kredit (Kas Keluar)</label>
            <div class="relative">
                <span class="absolute left-3 top-2.5 text-sipbo-text-muted dark:text-light-text-muted text-sm">Rp</span>
                <input type="number" step="0.01" name="kredit"
                    value="{{ $pengajuan?->nominal_usulan }}" required
                    class="w-full bg-sipbo-bg dark:bg-light-panel-light
                           border border-sipbo-border dark:border-light-border
                           rounded-xl p-2.5 pl-9 text-sm text-sipbo-text dark:text-light-text
                           focus:ring-2 focus:ring-sipbo-gold">
            </div>
        </div>

        <div class="flex gap-3 pt-1">
            <a href="{{ route('kasium.bku.index') }}"
                class="border border-sipbo-border dark:border-light-border
                       text-sipbo-text-muted dark:text-light-text-muted
                       px-4 py-2 rounded-xl text-sm
                       hover:bg-sipbo-panel-light dark:hover:bg-light-panel-light transition">
                Batal
            </a>
            <button class="bg-sipbo-gold hover:bg-sipbo-gold-light text-sipbo-bg
                           px-4 py-2 rounded-xl text-sm font-semibold transition active:scale-[0.98]">
                Simpan Transaksi
            </button>
        </div>
    </form>
</div>
@endsection