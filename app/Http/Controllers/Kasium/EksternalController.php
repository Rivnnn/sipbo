<?php
// app/Http/Controllers/Kasium/EksternalController.php

namespace App\Http\Controllers\Kasium;

use App\Http\Controllers\Controller;
use App\Models\PengajuanAnggaran;
use App\Services\PengajuanService;
use Illuminate\Http\Request;
use App\Http\Requests\AjukanPolrestabesRequest;

class EksternalController extends Controller
{
    public function __construct(protected PengajuanService $pengajuanService) {}

    public function ajukanPolrestabes(AjukanPolrestabesRequest $request, $id)
    {
        $pengajuan = PengajuanAnggaran::findOrFail($id);

        try {
            $this->pengajuanService->transisi(
                $pengajuan,
                'diajukan_ke_polrestabes',
                auth()->user(),
                'Diajukan via ASTINA',
                $request->validated('nomor_referensi_astina')
            );
            return back()->with('success', 'Pengajuan ditandai sebagai diajukan ke Polrestabes.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function danaCair($id)
    {
        $pengajuan = PengajuanAnggaran::findOrFail($id);

        try {
            $this->pengajuanService->transisi($pengajuan, 'dana_cair', auth()->user(), 'Dana telah cair dari Polrestabes');

            return redirect()->route('kasium.bku.create', ['pengajuan_id' => $pengajuan->id])
                ->with('success', 'Dana cair tercatat. Silakan input ke Buku Kas Umum.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
