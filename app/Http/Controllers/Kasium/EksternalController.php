<?php
// app/Http/Controllers/Kasium/EksternalController.php

namespace App\Http\Controllers\Kasium;

use App\Http\Controllers\Controller;
use App\Models\PengajuanAnggaran;
use App\Services\PengajuanService;
use App\Services\BkuService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\AjukanPolrestabesRequest;

class EksternalController extends Controller
{
    public function __construct(
        protected PengajuanService $pengajuanService,
        protected BkuService $bkuService
    ) {}

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
        $pengajuan = PengajuanAnggaran::with('programAnggaran')->findOrFail($id);

        try {
            DB::transaction(function () use ($pengajuan) {
                // 1) Ubah status pengajuan -> dana_cair
                $this->pengajuanService->transisi(
                    $pengajuan,
                    'dana_cair',
                    auth()->user(),
                    'Dana telah cair dari Polrestabes'
                );

                // 2) Catat DEBIT (dana masuk) ke Buku Kas Umum sebesar nominal usulan.
                //    Tanpa langkah ini, saldo_berjalan & total_dana_cair tidak akan
                //    pernah terupdate meski status pengajuan sudah "dana_cair".
                $this->bkuService->inputDebit([
                    'pengajuan_anggaran_id' => $pengajuan->id,
                    'program_anggaran_id'   => $pengajuan->program_anggaran_id,
                    'tanggal_transaksi'     => now()->toDateString(),
                    'uraian'                => 'Dana cair - ' . $pengajuan->judul_usulan
                        . ($pengajuan->nomor_referensi_astina ? ' (Ref ASTINA: ' . $pengajuan->nomor_referensi_astina . ')' : ''),
                    'debit'                 => $pengajuan->nominal_usulan,
                ], auth()->user());
            });

            return redirect()->route('kasium.bku.create', ['pengajuan_id' => $pengajuan->id])
                ->with('success', 'Dana cair tercatat di Buku Kas Umum. Silakan input distribusi ke unit.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
