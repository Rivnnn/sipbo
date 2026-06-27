<?php
// app/Http/Controllers/Kasium/VerifikasiController.php

namespace App\Http\Controllers\Kasium;

use App\Http\Controllers\Controller;
use App\Models\PengajuanAnggaran;
use App\Services\PengajuanService;
use Illuminate\Http\Request;
use App\Http\Requests\TolakPengajuanRequest;

class VerifikasiController extends Controller
{
    public function __construct(protected PengajuanService $pengajuanService) {}

    public function index()
    {
        $pengajuans = PengajuanAnggaran::with(['programAnggaran', 'unitKerja', 'user'])
            ->whereIn('status', ['menunggu_verifikasi', 'terverifikasi', 'disetujui_pimpinan', 'diajukan_ke_polrestabes'])
            ->latest('updated_at')
            ->paginate(10);

        return view('kasium.verifikasi.index', compact('pengajuans'));
    }

    public function verifikasi(Request $request, $id)
    {
        $pengajuan = PengajuanAnggaran::findOrFail($id);

        try {
            $this->pengajuanService->transisi(
                $pengajuan,
                'terverifikasi',
                auth()->user(),
                $request->input('catatan')
            );
            return back()->with('success', 'Pengajuan berhasil diverifikasi.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }


    public function tolak(TolakPengajuanRequest $request, $id)
    {
        $pengajuan = PengajuanAnggaran::findOrFail($id);

        try {
            $this->pengajuanService->transisi($pengajuan, 'ditolak', auth()->user(), $request->validated('catatan'));
            return back()->with('success', 'Pengajuan ditolak.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
