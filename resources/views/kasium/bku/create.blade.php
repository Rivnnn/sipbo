@extends('layouts.app')
@section('title', 'Catat Transaksi Kas')

@section('content')
<style>
    [x-cloak] {
        display: none !important;
    }
</style>
@php
// Peta saldo berjalan tiap program, dipakai JS untuk live preview
// saat kasium ganti pilihan program (kasus tanpa pengajuan terkait).
$saldoMap = $programs->mapWithKeys(fn($p) => [$p->id => (float) $p->saldo_berjalan]);
$saldoAwal = $pengajuan
? (float) $pengajuan->programAnggaran->saldo_berjalan
: (float) ($programs->first()->saldo_berjalan ?? 0);
$namaProgramAwal = $pengajuan
? $pengajuan->programAnggaran->nama_program
: ($programs->first()->nama_program ?? '-');
@endphp

<div class="max-w-4xl mx-auto"
    x-data="{
        kredit: {{ (float) old('kredit', $pengajuan?->nominal_usulan ?? 0) }},
        saldo: {{ $saldoAwal }},
        namaProgram: @js($namaProgramAwal),
        saldoMap: @js($saldoMap),
        rupiah(v) {
            v = Number(v || 0);
            return 'Rp ' + v.toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
        },
        onProgramChange(select) {
            const opt = select.options[select.selectedIndex];
            this.namaProgram = opt.dataset.nama || '-';
            this.saldo = this.saldoMap[select.value] ?? 0;
        },
        get sisa() { return this.saldo - (this.kredit || 0); },
        get overLimit() { return this.kredit > 0 && this.sisa < 0; }
    }">

    <!-- HEADER -->
    <div class="mb-5">
        <p class="text-xs font-medium text-sipbo-gold tracking-wide uppercase mb-1">Buku Kas Umum</p>
        <h1 class="text-lg lg:text-xl font-bold text-sipbo-text dark:text-light-text">Catat Transaksi Distribusi Dana</h1>
        <p class="text-sm text-sipbo-text-muted dark:text-light-text-muted mt-1">
            Isi detail transaksi kas keluar di bawah ini. Saldo akan diperbarui otomatis setelah tersimpan.
        </p>
    </div>

    @if($errors->any())
    <div class="flash-error rounded-xl p-3 mb-5 text-sm">
        @foreach($errors->all() as $error)
        <p>{{ $error }}</p>
        @endforeach
    </div>
    @endif

    <form action="{{ route('kasium.bku.store') }}" method="POST"
        @submit="kredit = Number(String(kredit).replace(/[^0-9.]/g,'')) || 0">
        @csrf

        <div class="grid lg:grid-cols-5 gap-5 items-start">

            <!-- KOLOM FORM -->
            <div class="lg:col-span-3 space-y-4">

                @if($pengajuan)
                <input type="hidden" name="pengajuan_anggaran_id" value="{{ $pengajuan->id }}">
                <input type="hidden" name="program_anggaran_id" value="{{ $pengajuan->program_anggaran_id }}">
                @endif

                <!-- CARD: SUMBER DANA -->
                <div class="bg-sipbo-panel dark:bg-light-panel
                            border border-sipbo-border dark:border-light-border
                            dark:shadow-sm rounded-2xl p-4 lg:p-5">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-7 h-7 rounded-lg bg-sipbo-gold/15 flex items-center justify-center flex-shrink-0">
                            <i data-lucide="wallet" class="w-4 h-4 text-sipbo-gold"></i>
                        </div>
                        <h3 class="text-sm font-semibold text-sipbo-text dark:text-light-text">Sumber Dana</h3>
                    </div>

                    @if($pengajuan)
                    <div class="bg-sipbo-gold/10 border border-sipbo-gold/30 rounded-xl p-3">
                        <p class="text-sipbo-text-muted dark:text-light-text-muted text-xs mb-0.5">Terkait pengajuan</p>
                        <p class="font-semibold text-sipbo-gold text-sm">{{ $pengajuan->judul_usulan }}</p>
                        <p class="text-sipbo-text-muted dark:text-light-text-muted text-xs mt-1">
                            Nominal diajukan: Rp {{ number_format($pengajuan->nominal_usulan, 0, ',', '.') }}
                        </p>
                        <p class="text-sipbo-text dark:text-light-text text-xs mt-2 pt-2 border-t border-sipbo-gold/20">
                            Program: <span class="font-medium">{{ $pengajuan->programAnggaran->nama_program }}</span>
                        </p>
                    </div>
                    @else
                    <div>
                        <label class="block text-sm text-sipbo-text dark:text-light-text mb-1.5">Program Anggaran</label>
                        <select name="program_anggaran_id" required
                            @change="onProgramChange($event.target)"
                            class="w-full bg-sipbo-bg dark:bg-light-panel-light
                                   border border-sipbo-border dark:border-light-border
                                   rounded-xl p-2.5 text-sm text-sipbo-text dark:text-light-text
                                   focus:ring-2 focus:ring-sipbo-gold focus:outline-none transition">
                            <option value="">-- Pilih Program --</option>
                            @foreach($programs as $p)
                            <option value="{{ $p->id }}"
                                data-nama="{{ $p->nama_program }}"
                                {{ old('program_anggaran_id') == $p->id ? 'selected' : '' }}>
                                {{ $p->nama_program }} &mdash; Saldo: Rp {{ number_format($p->saldo_berjalan, 0, ',', '.') }}
                            </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-sipbo-text-muted dark:text-light-text-muted mt-1.5">
                            Pilih program yang dananya akan didistribusikan.
                        </p>
                    </div>
                    @endif
                </div>

                <!-- CARD: DETAIL TRANSAKSI -->
                <div class="bg-sipbo-panel dark:bg-light-panel
                            border border-sipbo-border dark:border-light-border
                            dark:shadow-sm rounded-2xl p-4 lg:p-5 space-y-4">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-sipbo-gold/15 flex items-center justify-center flex-shrink-0">
                            <i data-lucide="file-text" class="w-4 h-4 text-sipbo-gold"></i>
                        </div>
                        <h3 class="text-sm font-semibold text-sipbo-text dark:text-light-text">Detail Transaksi</h3>
                    </div>

                    <div>
                        <label class="block text-sm text-sipbo-text dark:text-light-text mb-1.5">Tanggal Transaksi</label>
                        <div class="relative">
                            <i data-lucide="calendar-days"
                                class="w-4 h-4 text-sipbo-text-muted dark:text-light-text-muted absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                            <input type="date" name="tanggal_transaksi"
                                value="{{ old('tanggal_transaksi', now()->format('Y-m-d')) }}"
                                max="{{ now()->format('Y-m-d') }}" required
                                class="w-full bg-sipbo-bg dark:bg-light-panel-light
                                       border border-sipbo-border dark:border-light-border
                                       rounded-xl p-2.5 pl-9 text-sm text-sipbo-text dark:text-light-text
                                       focus:ring-2 focus:ring-sipbo-gold focus:outline-none transition">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm text-sipbo-text dark:text-light-text mb-1.5">Uraian</label>
                        <textarea name="uraian" required rows="2"
                            placeholder="Contoh: Distribusi dana ke Unit Lalu Lintas"
                            class="w-full bg-sipbo-bg dark:bg-light-panel-light
                                   border border-sipbo-border dark:border-light-border
                                   rounded-xl p-2.5 text-sm text-sipbo-text dark:text-light-text
                                   placeholder-sipbo-text-muted resize-none
                                   focus:ring-2 focus:ring-sipbo-gold focus:outline-none transition">{{ old('uraian', $pengajuan ? 'Distribusi dana - ' . $pengajuan->judul_usulan : '') }}</textarea>
                    </div>
                </div>

                <!-- CARD: NOMINAL -->
                <div class="bg-sipbo-panel dark:bg-light-panel
                            border border-sipbo-border dark:border-light-border
                            dark:shadow-sm rounded-2xl p-4 lg:p-5">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-7 h-7 rounded-lg bg-sipbo-gold/15 flex items-center justify-center flex-shrink-0">
                            <i data-lucide="banknote" class="w-4 h-4 text-sipbo-gold"></i>
                        </div>
                        <h3 class="text-sm font-semibold text-sipbo-text dark:text-light-text">Nominal Kredit (Kas Keluar)</h3>
                    </div>

                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sipbo-gold text-sm font-semibold">Rp</span>
                        <input type="text" inputmode="numeric" required
                            x-model.number="kredit"
                            name="kredit"
                            class="w-full bg-sipbo-bg dark:bg-light-panel-light
                                   border rounded-xl p-3 pl-10 text-lg font-semibold
                                   text-sipbo-text dark:text-light-text
                                   focus:ring-2 focus:outline-none transition"
                            :class="overLimit
                                ? 'border-red-500 focus:ring-red-500'
                                : 'border-sipbo-border dark:border-light-border focus:ring-sipbo-gold'">
                    </div>

                    <p class="text-xs mt-2 flex items-center gap-1.5"
                        :class="overLimit ? 'text-red-400' : 'text-sipbo-text-muted dark:text-light-text-muted'">
                        <i data-lucide="alert-triangle" class="w-3.5 h-3.5" x-show="overLimit" x-cloak></i>
                        <span x-text="overLimit
                            ? 'Nominal melebihi saldo berjalan program ini!'
                            : 'Masukkan tanpa titik/koma, cukup angka saja.'"></span>
                    </p>
                </div>

                <!-- ACTIONS (desktop) -->
                <div class="hidden lg:flex gap-3 pt-1">
                    <a href="{{ route('kasium.bku.index') }}"
                        class="border border-sipbo-border dark:border-light-border
                               text-sipbo-text-muted dark:text-light-text-muted
                               px-4 py-2.5 rounded-xl text-sm
                               hover:bg-sipbo-panel-light dark:hover:bg-light-panel-light transition">
                        Batal
                    </a>
                    <button type="submit" :disabled="overLimit"
                        class="bg-sipbo-gold hover:bg-sipbo-gold-light text-sipbo-bg
                               px-5 py-2.5 rounded-xl text-sm font-semibold transition active:scale-[0.98]
                               disabled:opacity-40 disabled:cursor-not-allowed disabled:active:scale-100
                               flex items-center gap-2">
                        <i data-lucide="check" class="w-4 h-4"></i>
                        Simpan Transaksi
                    </button>
                </div>
            </div>

            <!-- KOLOM RINGKASAN (sticky) -->
            <div class="lg:col-span-2">
                <div class="lg:sticky lg:top-4 bg-sipbo-gold/[0.06] dark:bg-amber-50/60
                            border border-sipbo-gold/25 dark:border-amber-200
                            rounded-2xl p-4 lg:p-5 space-y-4">
                    <div class="flex items-center gap-2">
                        <i data-lucide="calculator" class="w-4 h-4 text-sipbo-gold"></i>
                        <h3 class="text-sm font-semibold text-sipbo-gold">Ringkasan</h3>
                    </div>

                    <div>
                        <p class="text-xs text-sipbo-text-muted dark:text-light-text-muted">Program</p>
                        <p class="text-sm font-medium text-sipbo-text dark:text-light-text" x-text="namaProgram"></p>
                    </div>

                    <div class="h-px bg-sipbo-gold/20"></div>

                    <div class="flex justify-between items-baseline">
                        <p class="text-xs text-sipbo-text-muted dark:text-light-text-muted">Saldo berjalan saat ini</p>
                        <p class="text-sm font-semibold text-sipbo-text dark:text-light-text" x-text="rupiah(saldo)"></p>
                    </div>

                    <div class="flex justify-between items-baseline">
                        <p class="text-xs text-sipbo-text-muted dark:text-light-text-muted">Nominal transaksi</p>
                        <p class="text-sm font-semibold text-red-400" x-text="'- ' + rupiah(kredit)"></p>
                    </div>

                    <div class="h-px bg-sipbo-gold/20"></div>

                    <div class="flex justify-between items-baseline">
                        <p class="text-xs font-medium text-sipbo-text dark:text-light-text">Saldo setelah transaksi</p>
                        <p class="text-base font-bold"
                            :class="overLimit ? 'text-red-400' : 'text-green-500'"
                            x-text="rupiah(sisa)"></p>
                    </div>

                    <div x-show="overLimit" x-cloak
                        class="flex items-start gap-2 bg-red-900/20 dark:bg-red-50 border border-red-700/30 dark:border-red-200 rounded-xl p-3">
                        <i data-lucide="alert-triangle" class="w-4 h-4 text-red-400 flex-shrink-0 mt-0.5"></i>
                        <p class="text-xs text-red-400">
                            Transaksi tidak bisa disimpan &mdash; nominal melebihi saldo yang tersedia di program ini.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- ACTIONS (mobile, sticky bottom) -->
        <div class="lg:hidden sticky bottom-0 -mx-4 mt-5 p-4 bg-sipbo-bg dark:bg-light-bg
                    border-t border-sipbo-border dark:border-light-border flex gap-3">
            <a href="{{ route('kasium.bku.index') }}"
                class="flex-1 text-center border border-sipbo-border dark:border-light-border
                       text-sipbo-text-muted dark:text-light-text-muted
                       px-4 py-2.5 rounded-xl text-sm
                       hover:bg-sipbo-panel-light dark:hover:bg-light-panel-light transition">
                Batal
            </a>
            <button type="submit" :disabled="overLimit"
                class="flex-1 bg-sipbo-gold hover:bg-sipbo-gold-light text-sipbo-bg
                       px-4 py-2.5 rounded-xl text-sm font-semibold transition active:scale-[0.98]
                       disabled:opacity-40 disabled:cursor-not-allowed">
                Simpan Transaksi
            </button>
        </div>
    </form>
</div>
@endsection