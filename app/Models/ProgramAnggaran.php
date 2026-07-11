<?php
// app/Models/ProgramAnggaran.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramAnggaran extends Model
{
    protected $guarded = [];

    protected $casts = [
        'pagu_dipa'       => 'decimal:2',
        'tahun_anggaran'  => 'integer',
    ];

    public function pengajuans()
    {
        return $this->hasMany(PengajuanAnggaran::class);
    }

    public function bukuKasUmums()
    {
        return $this->hasMany(BukuKasUmum::class);
    }

    // Saldo BKU berjalan (dari transaksi terakhir)
    public function getSaldoBerjalanAttribute(): float
    {
        $last = $this->bukuKasUmums()
            ->latest('tanggal_transaksi')
            ->latest('id')
            ->first();

        return $last ? (float) $last->saldo : 0;
    }

    // Total dana yang sudah dicairkan dari Polrestabes
    public function getTotalDanaCairAttribute(): float
    {
        return (float) $this->bukuKasUmums()->sum('debit');
    }

    // Total yang sudah didistribusikan ke unit
    public function getTotalDistribusiAttribute(): float
    {
        return (float) $this->bukuKasUmums()->sum('kredit');
    }

    // Sisa pagu DIPA yang belum dicairkan
    public function getSisaPaguAttribute(): float
    {
        return (float) $this->pagu_dipa - $this->total_dana_cair;
    }

    // Persentase realisasi
    public function getPersentaseRealisasiAttribute(): float
    {
        if ((float)$this->pagu_dipa <= 0) return 0;
        return round(($this->total_distribusi / (float)$this->pagu_dipa) * 100, 2);
    }
}
