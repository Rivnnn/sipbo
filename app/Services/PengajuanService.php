<?php
// app/Services/PengajuanService.php

namespace App\Services;

use App\Models\{PengajuanAnggaran, AuditTrail};
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PengajuanService
{
    private array $transitions = [
        'draft'                   => ['menunggu_verifikasi'],
        'menunggu_verifikasi'     => ['terverifikasi', 'ditolak'],
        'terverifikasi'           => ['disetujui_pimpinan', 'ditolak'],
        'disetujui_pimpinan'      => ['diajukan_ke_polrestabes'],
        'diajukan_ke_polrestabes' => ['dana_cair'],
        'dana_cair'               => [],
        'ditolak'                 => [],
    ];

    private array $actorGuard = [
        'menunggu_verifikasi'     => ['staf_unit', 'kasium'],
        'terverifikasi'           => ['kasium'],
        'disetujui_pimpinan'      => ['pimpinan'],
        'diajukan_ke_polrestabes' => ['kasium'],
        'dana_cair'               => ['kasium'],
        'ditolak'                 => ['kasium', 'pimpinan'],
    ];

    private array $timestampMap = [
        'menunggu_verifikasi'     => 'diajukan_pada',
        'terverifikasi'           => 'terverifikasi_pada',
        'disetujui_pimpinan'      => 'acc_pimpinan_pada',
        'diajukan_ke_polrestabes' => 'diajukan_polrestabes_pada',
        'dana_cair'               => 'dana_cair_pada',
    ];

    public function transisi(
        PengajuanAnggaran $pengajuan,
        string $statusBaru,
        User $actor,
        ?string $catatan = null,
        ?string $noRefAstina = null
    ): void {
        $statusLama   = $pengajuan->status;
        $allowedRoles = $this->actorGuard[$statusBaru] ?? [];

        // Validasi transisi status
        if (!in_array($statusBaru, $this->transitions[$statusLama] ?? [])) {
            throw new \Exception(
                "Transisi status tidak valid: {$statusLama} → {$statusBaru}"
            );
        }

        // Validasi role aktor
        if (!empty($allowedRoles) && !$actor->hasAnyRole($allowedRoles)) {
            throw new \Exception(
                "Anda tidak memiliki izin untuk melakukan tindakan ini."
            );
        }

        DB::transaction(function () use (
            $pengajuan,
            $statusLama,
            $statusBaru,
            $actor,
            $catatan,
            $noRefAstina
        ) {
            $pengajuan->status = $statusBaru;

            // Set timestamp
            if (isset($this->timestampMap[$statusBaru])) {
                $pengajuan->{$this->timestampMap[$statusBaru]} = now();
            }

            // Set relasi
            if ($statusBaru === 'terverifikasi') {
                $pengajuan->verified_by = $actor->id;
            }
            if ($statusBaru === 'disetujui_pimpinan') {
                $pengajuan->approved_by = $actor->id;
            }
            if ($noRefAstina) {
                $pengajuan->nomor_referensi_astina = $noRefAstina;
            }

            $pengajuan->save();

            // Simpan audit trail
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
