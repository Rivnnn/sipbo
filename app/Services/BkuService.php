<?php

namespace App\Services;

use App\Models\BukuKasUmum;
use App\Models\ProgramAnggaran;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class BkuService
{
    public function inputKredit(array $data, User $actor): BukuKasUmum
    {
        $program       = ProgramAnggaran::findOrFail($data['program_anggaran_id']);
        $saldoBerjalan = $this->hitungSaldoBerjalan($program->id);

        if ($data['kredit'] > $saldoBerjalan) {
            throw new \Exception(
                "Transaksi diblokir: nominal Rp " . number_format($data['kredit'], 2) .
                    " melebihi saldo berjalan Rp " . number_format($saldoBerjalan, 2) . "."
            );
        }

        $saldoBaru = $saldoBerjalan - $data['kredit'];

        return DB::transaction(function () use ($data, $actor, $saldoBaru) {
            return BukuKasUmum::create([
                ...$data,
                'debit'    => 0,
                'saldo'    => $saldoBaru,
                'input_by' => $actor->id,
            ]);
        });
    }

    public function hitungSaldoBerjalan(int $programId): float
    {
        $last = BukuKasUmum::where('program_anggaran_id', $programId)
            ->latest('tanggal_transaksi')
            ->latest('id')
            ->first();

        return $last
            ? (float) $last->saldo
            : (float) ProgramAnggaran::findOrFail($programId)->pagu_dipa;
    }
}
