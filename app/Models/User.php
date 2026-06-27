<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles, SoftDeletes;

    protected $guarded = [];
    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return ['password' => 'hashed'];
    }

    public function unitKerja()
    {
        return $this->belongsTo(UnitKerja::class);
    }

    public function pengajuans()
    {
        return $this->hasMany(PengajuanAnggaran::class);
    }

    public function getRoleLabelAttribute(): string
    {
        return match ($this->getRoleNames()->first()) {
            'staf_unit' => 'Staf Unit',
            'kasium' => 'KASIUM',
            'pimpinan' => 'Pimpinan',
            default => '-',
        };
    }
}
