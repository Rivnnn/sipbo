<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PengajuanAnggaran extends Model
{
    use SoftDeletes;
    protected $guarded = [];
    protected $casts = [
        'nominal_usulan' => 'decimal:2',
        'diajukan_pada' => 'datetime',
        'terverifikasi_pada' => 'datetime',
        'acc_pimpinan_pada' => 'datetime',
        'diajukan_polrestabes_pada' => 'datetime',
        'dana_cair_pada' => 'datetime',
    ];

    public function unitKerja()
    {
        return $this->belongsTo(UnitKerja::class);
    }
    public function programAnggaran()
    {
        return $this->belongsTo(ProgramAnggaran::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
    public function bukuKasUmum()
    {
        return $this->hasOne(BukuKasUmum::class);
    }
    public function auditTrails()
    {
        return $this->hasMany(AuditTrail::class)->latest('waktu_eksekusi');
    }

    public function statusBadge(): array
    {
        return match ($this->status) {
            'draft'                   => ['badge-draft',    'Draft'],
            'menunggu_verifikasi'     => ['badge-menunggu', 'Verifikasi KASIUM'],
            'terverifikasi'           => ['badge-verif',    'Terverifikasi'],
            'disetujui_pimpinan'      => ['badge-setuju',   'Diajukan ke Kapolsek'],
            'diajukan_ke_polrestabes' => ['badge-verif',    'Diajukan Polrestabes'],
            'dana_cair'               => ['badge-cair',     'Dana Cair'],
            'ditolak'                 => ['badge-tolak',    'Ditolak'],
            default                   => ['badge-draft',    $this->status],
        };
    }
}
