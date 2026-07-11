<?php
// app/Services/BkuService.php

namespace App\Services;

use App\Models\{BukuKasUmum, ProgramAnggaran};
use App\Models\User;
use Illuminate\Support\Facades\DB;

class BkuService
{
    public function hitungSaldoBerjalan(int $programId): float
    {
        $last = BukuKasUmum::where('program_anggaran_id', $programId)
            ->latest('tanggal_transaksi')
            ->latest('id')
            ->first();

        return $last ? (float) $last->saldo : 0;
    }

    public function hitungTotalDanaCair(int $programId): float
    {
        return (float) BukuKasUmum::where('program_anggaran_id', $programId)
            ->sum('debit');
    }

    public function inputDebit(array $data, User $actor): BukuKasUmum
    {
        $program        = ProgramAnggaran::findOrFail($data['program_anggaran_id']);
        $totalSudahCair = $this->hitungTotalDanaCair($data['program_anggaran_id']);
        $saldoBerjalan  = $this->hitungSaldoBerjalan($data['program_anggaran_id']);

        // Validasi tidak melebihi pagu DIPA
        $totalSetelahCair = $totalSudahCair + (float)$data['debit'];
        if ($totalSetelahCair > (float)$program->pagu_dipa) {
            throw new \Exception(
                "Dana cair diblokir: total dana yang akan dicairkan (Rp " .
                    number_format($totalSetelahCair, 0, ',', '.') .
                    ") melebihi Pagu DIPA (Rp " .
                    number_format($program->pagu_dipa, 0, ',', '.') . ")."
            );
        }

        $saldoBaru = $saldoBerjalan + (float)$data['debit'];

        return DB::transaction(function () use ($data, $actor, $saldoBaru) {
            return BukuKasUmum::create([
                'pengajuan_anggaran_id' => $data['pengajuan_anggaran_id'],
                'program_anggaran_id'   => $data['program_anggaran_id'],
                'tanggal_transaksi'     => $data['tanggal_transaksi'] ?? now()->toDateString(),
                'uraian'                => $data['uraian'],
                'debit'                 => $data['debit'],
                'kredit'                => 0,
                'saldo'                 => $saldoBaru,
                'input_by'              => $actor->id,
            ]);
        });
    }

    public function inputKredit(array $data, User $actor): BukuKasUmum
    {
        $saldoBerjalan = $this->hitungSaldoBerjalan($data['program_anggaran_id']);

        if ($saldoBerjalan <= 0) {
            throw new \Exception(
                "Transaksi diblokir: belum ada dana yang cair. " .
                    "Tandai dana cair terlebih dahulu."
            );
        }

        if ((float)$data['kredit'] > $saldoBerjalan) {
            throw new \Exception(
                "Transaksi diblokir: nominal distribusi Rp " .
                    number_format($data['kredit'], 0, ',', '.') .
                    " melebihi saldo berjalan Rp " .
                    number_format($saldoBerjalan, 0, ',', '.') . "."
            );
        }

        $saldoBaru = $saldoBerjalan - (float)$data['kredit'];

        return DB::transaction(function () use ($data, $actor, $saldoBaru) {
            return BukuKasUmum::create([
                'pengajuan_anggaran_id' => $data['pengajuan_anggaran_id'] ?? null,
                'program_anggaran_id'   => $data['program_anggaran_id'],
                'tanggal_transaksi'     => $data['tanggal_transaksi'],
                'uraian'                => $data['uraian'],
                'debit'                 => 0,
                'kredit'                => $data['kredit'],
                'saldo'                 => $saldoBaru,
                'input_by'              => $actor->id,
            ]);
        });
    }

    // Recalculate saldo dari awal jika ada inkonsistensi
    public function recalculateSaldo(int $programId): void
    {
        $transaksi = BukuKasUmum::where('program_anggaran_id', $programId)
            ->orderBy('tanggal_transaksi')
            ->orderBy('id')
            ->get();

        $saldo = 0;
        foreach ($transaksi as $t) {
            $saldo = $saldo + (float)$t->debit - (float)$t->kredit;
            $t->update(['saldo' => $saldo]);
        }
    }
}
