<?php
// app/Http/Controllers/Pimpinan/ApprovalController.php

namespace App\Http\Controllers\Pimpinan;

use App\Http\Controllers\Controller;
use App\Models\PengajuanAnggaran;
use App\Services\PengajuanService;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    public function __construct(protected PengajuanService $pengajuanService) {}

    public function index()
    {
        $pengajuans = PengajuanAnggaran::with(['programAnggaran', 'unitKerja', 'user', 'verifier'])
            ->where('status', 'terverifikasi')
            ->latest('terverifikasi_pada')
            ->paginate(10);

        return view('pimpinan.approval.index', compact('pengajuans'));
    }

    public function setujui(Request $request, $id)
    {
        $pengajuan = PengajuanAnggaran::findOrFail($id);

        try {
            $this->pengajuanService->transisi(
                $pengajuan,
                'disetujui_pimpinan',
                auth()->user(),
                $request->input('catatan', 'Disetujui oleh Pimpinan')
            );
            return back()->with('success', 'Pengajuan disetujui.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function tolak(Request $request, $id)
    {
        $request->validate(['catatan' => 'required|string|min:5']);

        $pengajuan = PengajuanAnggaran::findOrFail($id);

        try {
            $this->pengajuanService->transisi(
                $pengajuan,
                'ditolak',
                auth()->user(),
                $request->input('catatan')
            );
            return back()->with('success', 'Pengajuan ditolak.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
