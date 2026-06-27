<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Model;

class AuditTrail extends Model
{
    protected $guarded = [];

    protected $casts = ['waktu_eksekusi' => 'datetime'];

    public function pengajuanAnggaran()
    {
        return $this->belongsTo(PengajuanAnggaran::class);
    }

    public function actor()
    {
        return $this->belongsTo(User::class);
    }
}
