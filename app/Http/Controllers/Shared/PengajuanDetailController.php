<?php
// app/Http/Controllers/Shared/PengajuanDetailController.php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Models\PengajuanAnggaran;

class PengajuanDetailController extends Controller
{
    public function show($id)
    {
        $pengajuan = PengajuanAnggaran::with([
            'programAnggaran',
            'unitKerja',
            'user',
            'verifier',
            'approver',
            'auditTrails.actor',
            'bukuKasUmum'
        ])->findOrFail($id);

        $this->authorizeView($pengajuan);

        return view('shared.pengajuan.show', compact('pengajuan'));
    }

    private function authorizeView(PengajuanAnggaran $pengajuan): void
    {
        $user = auth()->user();

        if ($user->hasRole('staf_unit') && $pengajuan->user_id !== $user->id) {
            abort(403);
        }
    }
}
