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
            <x-btn-primary size="sm" onclick="openModal('bku-create')">
                <i data-lucide="plus" class="w-4 h-4"></i> Catat Transaksi
            </x-btn-primary>
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

{{-- ============ MODAL CATAT TRANSAKSI ============ --}}
<x-modal id="bku-create" title="Catat Transaksi Kas" size="md">
    <form action="{{ route('kasium.bku.store') }}" method="POST" class="space-y-4">
        @csrf

        {{-- Warning jika saldo 0 --}}
        @if($program && $program->saldo_berjalan <= 0)
            <div class="bg-red-900/30 dark:bg-red-50 border border-red-700/50 dark:border-red-200
                    text-red-400 dark:text-red-700 text-sm rounded-lg p-3">
            <p class="font-semibold">⚠ Saldo BKU masih Rp 0</p>
            <p class="text-xs mt-1">
                Dana belum cair untuk program ini. Pastikan pengajuan sudah
                berstatus <strong>Dana Cair</strong> sebelum mencatat distribusi.
            </p>
            </div>
            @endif

            <div>
                <label class="block text-sm text-sipbo-text dark:text-light-text mb-1">
                    Program Anggaran
                </label>
                <select name="program_anggaran_id" required
                    class="sipbo-input" id="bku-program-select">
                    <option value="">-- Pilih Program --</option>
                    @foreach($programs as $p)
                    <option value="{{ $p->id }}"
                        data-saldo="{{ $p->saldo_berjalan }}"
                        data-pagu="{{ $p->pagu_dipa }}"
                        {{ $programId == $p->id ? 'selected' : '' }}>
                        {{ $p->nama_program }} ({{ $p->kode_program }})
                    </option>
                    @endforeach
                </select>

                {{-- Info saldo real-time --}}
                <div class="mt-1.5 text-xs space-y-0.5" id="bku-saldo-info-wrap">
                    <p class="text-sipbo-text-muted dark:text-light-text-muted">
                        Saldo BKU berjalan:
                        <span id="bku-saldo-val" class="font-semibold text-sipbo-gold">-</span>
                    </p>
                    <p id="bku-warning" class="text-red-400 dark:text-red-600 hidden">
                        ⚠ Saldo 0 — tandai dana cair terlebih dahulu
                    </p>
                </div>
            </div>

            <div>
                <label class="block text-sm text-sipbo-text dark:text-light-text mb-1">
                    Tanggal Transaksi
                </label>
                <input type="date" name="tanggal_transaksi"
                    value="{{ now()->format('Y-m-d') }}"
                    required class="sipbo-input">
            </div>

            <div>
                <label class="block text-sm text-sipbo-text dark:text-light-text mb-1">
                    Uraian
                </label>
                <input type="text" name="uraian" required class="sipbo-input"
                    placeholder="Contoh: Distribusi dana ke Unit Lalu Lintas">
            </div>

            <div>
                <label class="block text-sm text-sipbo-text dark:text-light-text mb-1">
                    Nominal Kredit (Kas Keluar / Distribusi)
                </label>
                <div class="relative">
                    <span class="absolute left-3 top-2.5 text-sipbo-text-muted
                             dark:text-light-text-muted text-sm font-medium">Rp</span>
                    <input type="number" step="1" name="kredit" required
                        class="sipbo-input pl-10" placeholder="0"
                        id="bku-kredit-input">
                </div>
                <p class="text-xs text-sipbo-text-muted dark:text-light-text-muted mt-1">
                    Tidak boleh melebihi saldo BKU berjalan
                </p>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <x-btn-secondary type="button" onclick="closeModal('bku-create')">
                    Batal
                </x-btn-secondary>
                <x-btn-primary type="submit" id="bku-submit-btn">
                    Simpan Transaksi
                </x-btn-primary>
            </div>
    </form>
</x-modal>

@push('scripts')
<script>
    const sel = document.getElementById('bku-program-select');
    const valEl = document.getElementById('bku-saldo-val');
    const warnEl = document.getElementById('bku-warning');
    const submitBtn = document.getElementById('bku-submit-btn');
    const kreditInput = document.getElementById('bku-kredit-input');

    function updateSaldoInfo() {
        const opt = sel.options[sel.selectedIndex];
        const saldo = parseFloat(opt.dataset.saldo ?? 0);

        if (opt.value) {
            valEl.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(saldo);

            if (saldo <= 0) {
                warnEl.classList.remove('hidden');
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            } else {
                warnEl.classList.add('hidden');
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        } else {
            valEl.textContent = '-';
        }
    }

    // Validasi nominal tidak boleh > saldo
    kreditInput?.addEventListener('input', function() {
        const opt = sel.options[sel.selectedIndex];
        const saldo = parseFloat(opt.dataset.saldo ?? 0);
        const val = parseFloat(this.value ?? 0);

        if (val > saldo && saldo > 0) {
            this.setCustomValidity(
                'Nominal melebihi saldo berjalan Rp ' +
                new Intl.NumberFormat('id-ID').format(saldo)
            );
        } else {
            this.setCustomValidity('');
        }
    });

    sel?.addEventListener('change', updateSaldoInfo);

    // Jalankan saat modal dibuka
    window.addEventListener('open-modal-bku-create', () => {
        updateSaldoInfo();
    });
</script>
@endpush

@endsection