<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BukuKasUmum extends Model
{
    protected $guarded = [];
    protected $casts = ['tanggal_transaksi' => 'date', 'debit' => 'decimal:2', 'kredit' => 'decimal:2', 'saldo' => 'decimal:2'];

    public function pengajuanAnggaran()
    {
        return $this->belongsTo(PengajuanAnggaran::class);
    }
    public function programAnggaran()
    {
        return $this->belongsTo(ProgramAnggaran::class);
    }
    public function inputBy()
    {
        return $this->belongsTo(User::class, 'input_by');
    }
}
