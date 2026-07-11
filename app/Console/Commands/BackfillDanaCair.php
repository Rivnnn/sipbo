<?php

namespace App\Console\Commands;

use App\Models\PengajuanAnggaran;
use App\Services\BkuService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BackfillDanaCair extends Command
{
    /**
     * Sebelum diperbaiki, mengubah status pengajuan jadi "dana_cair" tidak
     * pernah mencatat baris DEBIT di buku_kas_umums. Akibatnya pengajuan
     * yang sudah kadung berstatus dana_cair akan tetap gagal saat dicoba
     * didistribusikan (error "belum ada dana yang cair"), walau kode
     * sudah diperbaiki, karena data lama memang belum punya baris BKU.
     *
     * Command ini membuat baris debit yang seharusnya sudah ada, untuk
     * setiap pengajuan berstatus dana_cair yang belum punya bukuKasUmum.
     */
    protected $signature = 'bku:backfill-dana-cair {--dry-run : Tampilkan apa yang akan dibuat tanpa menyimpan}';

    protected $description = 'Backfill baris debit BKU untuk pengajuan dana_cair lama yang belum tercatat di Buku Kas Umum';

    public function handle(BkuService $bkuService): int
    {
        $dryRun = (bool) $this->option('dry-run');

        $pengajuans = PengajuanAnggaran::with('programAnggaran')
            ->where('status', 'dana_cair')
            ->doesntHave('bukuKasUmum')
            ->get();

        if ($pengajuans->isEmpty()) {
            $this->info('Tidak ada data yang perlu di-backfill. Semua pengajuan dana_cair sudah tercatat di BKU.');
            return self::SUCCESS;
        }

        $this->info("Ditemukan {$pengajuans->count()} pengajuan berstatus dana_cair tanpa baris BKU:");

        $actor = \App\Models\User::whereHas('roles', fn ($q) => $q->where('name', 'kasium'))->first()
            ?? \App\Models\User::first();

        if (!$actor) {
            $this->error('Tidak ada user ditemukan untuk dipakai sebagai pencatat (input_by). Backfill dibatalkan.');
            return self::FAILURE;
        }

        foreach ($pengajuans as $p) {
            $this->line(" - #{$p->id} {$p->judul_usulan} | Rp " . number_format($p->nominal_usulan, 0, ',', '.')
                . " | program: {$p->programAnggaran?->nama_program}");
        }

        if ($dryRun) {
            $this->warn('Dry run: tidak ada perubahan disimpan.');
            return self::SUCCESS;
        }

        if (!$this->confirm('Lanjutkan membuat baris debit untuk semua data di atas?', true)) {
            $this->warn('Dibatalkan.');
            return self::SUCCESS;
        }

        $ok = 0;
        $failed = 0;

        foreach ($pengajuans as $p) {
            try {
                DB::transaction(function () use ($p, $bkuService, $actor) {
                    $bkuService->inputDebit([
                        'pengajuan_anggaran_id' => $p->id,
                        'program_anggaran_id'   => $p->program_anggaran_id,
                        'tanggal_transaksi'     => optional($p->dana_cair_pada)->toDateString() ?? $p->updated_at->toDateString(),
                        'uraian'                => 'Dana cair - ' . $p->judul_usulan . ' (backfill)',
                        'debit'                 => $p->nominal_usulan,
                    ], $actor);
                });
                $ok++;
            } catch (\Throwable $e) {
                $failed++;
                $this->error("Gagal untuk pengajuan #{$p->id}: {$e->getMessage()}");
            }
        }

        $this->info("Selesai. Berhasil: {$ok}, Gagal: {$failed}.");

        if ($failed > 0) {
            $this->warn('Untuk yang gagal (biasanya karena melebihi pagu_dipa), cek manual dan sesuaikan pagu_dipa atau nominal_usulan-nya.');
        }

        return self::SUCCESS;
    }
}
