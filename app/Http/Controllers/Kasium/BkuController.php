<?php
// app/Http/Controllers/Kasium/BkuController.php

namespace App\Http\Controllers\Kasium;

use App\Http\Controllers\Controller;
use App\Models\{BukuKasUmum, ProgramAnggaran, PengajuanAnggaran};
use App\Services\BkuService;
use Illuminate\Http\Request;
use App\Http\Requests\StoreBkuRequest;

class BkuController extends Controller
{
    public function __construct(protected BkuService $bkuService) {}

    public function index(Request $request)
    {
        $programs = ProgramAnggaran::with('bukuKasUmums')->where('tahun_anggaran', now()->year)->get();
        $programId = $request->input('program_id', $programs->first()?->id);
        $transaksi = BukuKasUmum::with(['inputBy', 'pengajuanAnggaran'])
            ->where('program_anggaran_id', $programId)
            ->orderBy('tanggal_transaksi')->orderBy('id')
            ->paginate(15);
        $program = $programs->firstWhere('id', $programId);

        return view('kasium.bku.index', compact('programs', 'programId', 'transaksi', 'program'));
    }

    public function create(Request $request)
    {
        $programs = ProgramAnggaran::with('bukuKasUmums')->where('tahun_anggaran', now()->year)->get();

        $pengajuan = null;
        if ($request->has('pengajuan_id')) {
            $pengajuan = PengajuanAnggaran::with('programAnggaran.bukuKasUmums')->find($request->pengajuan_id);
        }

        return view('kasium.bku.create', compact('programs', 'pengajuan'));
    }

    public function store(StoreBkuRequest $request)
    {
        $validated = $request->validated();

        try {
            $this->bkuService->inputKredit($validated, auth()->user());

            return redirect()->route('kasium.bku.index', ['program_id' => $validated['program_anggaran_id']])
                ->with('success', 'Transaksi kas berhasil dicatat.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }
}
