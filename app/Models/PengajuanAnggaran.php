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
            'draft' => ['bg-gray-700 text-gray-300', 'Draft'],
            'menunggu_verifikasi' => ['bg-yellow-900/40 text-yellow-400', 'Verifikasi KASIUM'],
            'terverifikasi' => ['bg-blue-900/40 text-blue-400', 'Terverifikasi'],
            'disetujui_pimpinan' => ['bg-sipbo-gold/20 text-sipbo-gold', 'Diajukan ke Kapolsek'],
            'diajukan_ke_polrestabes' => ['bg-indigo-900/40 text-indigo-400', 'Diajukan Polrestabes'],
            'dana_cair' => ['bg-green-900/40 text-green-400', 'Dana Cair'],
            'ditolak' => ['bg-red-900/40 text-red-400', 'Ditolak'],
            default => ['bg-gray-700 text-gray-300', $this->status],
        };
    }
}
