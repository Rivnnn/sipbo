<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramAnggaran extends Model
{
    protected $guarded = [];
    public function pengajuans()
    {
        return $this->hasMany(PengajuanAnggaran::class);
    }
    public function bukuKasUmums()
    {
        return $this->hasMany(BukuKasUmum::class);
    }

    public function getSaldoBerjalanAttribute(): float
    {
        $last = $this->bukuKasUmums()->latest('tanggal_transaksi')->latest('id')->first();
        return $last ? (float) $last->saldo : (float) $this->pagu_dipa;
    }
}
