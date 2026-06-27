<?php

namespace App\Services; // ← huruf besar 'A'

use App\Models\AuditTrail;
use App\Models\PengajuanAnggaran;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PengajuanService
{
    private array $transitions = [
        'draft'                  => ['menunggu_verifikasi'],
        'menunggu_verifikasi'    => ['terverifikasi', 'ditolak'],
        'terverifikasi'          => ['disetujui_pimpinan', 'ditolak'],
        'disetujui_pimpinan'     => ['diajukan_ke_polrestabes'],
        'diajukan_ke_polrestabes' => ['dana_cair', 'ditolak'],
        'dana_cair'              => [],
        'ditolak'                => [],
    ];

    public function transisi(
        PengajuanAnggaran $pengajuan,
        string $statusBaru,
        User $actor,
        ?string $catatan = null,
        ?string $noRefAstina = null
    ): void {
        $statusLama = $pengajuan->status;

        if (!in_array($statusBaru, $this->transitions[$statusLama] ?? [])) {
            throw new \Exception("Transisi status tidak valid: {$statusLama} -> {$statusBaru}");
        }

        DB::transaction(function () use ($pengajuan, $statusLama, $statusBaru, $actor, $catatan, $noRefAstina) {
            $pengajuan->status = $statusBaru;

            $kolomWaktu = [
                'menunggu_verifikasi'     => 'diajukan_pada',
                'terverifikasi'           => 'terverifikasi_pada',
                'disetujui_pimpinan'      => 'acc_pimpinan_pada',
                'diajukan_ke_polrestabes' => 'diajukan_polrestabes_pada',
                'dana_cair'               => 'dana_cair_pada',
            ];

            if (isset($kolomWaktu[$statusBaru])) {
                $pengajuan->{$kolomWaktu[$statusBaru]} = now();
            }

            if ($statusBaru === 'terverifikasi')      $pengajuan->verified_by = $actor->id;
            if ($statusBaru === 'disetujui_pimpinan') $pengajuan->approved_by = $actor->id;
            if ($noRefAstina) $pengajuan->nomor_referensi_astina = $noRefAstina;

            $pengajuan->save();

            AuditTrail::create([
                'pengajuan_anggaran_id' => $pengajuan->id,
                'status_sebelum'        => $statusLama,
                'status_sesudah'        => $statusBaru,
                'actor_id'              => $actor->id,
                'catatan'               => $catatan,
                'waktu_eksekusi'        => now(),
            ]);
        });
    }
}
