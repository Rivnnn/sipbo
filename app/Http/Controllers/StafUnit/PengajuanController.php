<?php
// app/Http/Controllers/StafUnit/PengajuanController.php

namespace App\Http\Controllers\StafUnit;

use App\Http\Controllers\Controller;
use App\Models\{PengajuanAnggaran, ProgramAnggaran};
use App\Services\PengajuanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\{StorePengajuanRequest, UpdatePengajuanRequest};

class PengajuanController extends Controller
{
    public function __construct(protected PengajuanService $pengajuanService) {}

    public function index()
    {
        $pengajuans = PengajuanAnggaran::with(['programAnggaran', 'unitKerja'])
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('staf-unit.pengajuan.index', compact('pengajuans'));
    }

    public function create()
    {
        $programs = ProgramAnggaran::where('tahun_anggaran', now()->year)->get();
        return view('staf-unit.pengajuan.create', compact('programs'));
    }

    public function store(StorePengajuanRequest $request)
    {
        $validated = $request->validated();
        $validated['user_id'] = auth()->id();
        $validated['unit_kerja_id'] = auth()->user()->unit_kerja_id;
        $validated['status'] = 'draft';

        if ($request->hasFile('file_lampiran')) {
            $validated['file_lampiran'] = $request->file('file_lampiran')->store('lampiran-pengajuan', 'public');
        }

        $pengajuan = PengajuanAnggaran::create($validated);

        return redirect()->route('pengajuan.show', $pengajuan->id)
            ->with('success', 'Draft pengajuan berhasil dibuat.');
    }

    public function edit($id)
    {
        $pengajuan = PengajuanAnggaran::findOrFail($id);

        abort_unless($pengajuan->user_id === auth()->id() && $pengajuan->status === 'draft', 403);

        $programs = ProgramAnggaran::where('tahun_anggaran', now()->year)->get();
        return view('staf-unit.pengajuan.edit', compact('pengajuan', 'programs'));
    }

    public function update(UpdatePengajuanRequest $request, $id)
    {
        $pengajuan = PengajuanAnggaran::findOrFail($id);
        $validated = $request->validated();

        if ($request->hasFile('file_lampiran')) {
            if ($pengajuan->file_lampiran) {
                Storage::disk('public')->delete($pengajuan->file_lampiran);
            }
            $validated['file_lampiran'] = $request->file('file_lampiran')->store('lampiran-pengajuan', 'public');
        }

        $pengajuan->update($validated);

        return redirect()->route('pengajuan.show', $pengajuan->id)
            ->with('success', 'Pengajuan berhasil diperbarui.');
    }

    public function kirim($id)
    {
        $pengajuan = PengajuanAnggaran::findOrFail($id);
        abort_unless($pengajuan->user_id === auth()->id() && $pengajuan->status === 'draft', 403);

        try {
            $this->pengajuanService->transisi($pengajuan, 'menunggu_verifikasi', auth()->user());
            return redirect()->route('pengajuan.show', $pengajuan->id)
                ->with('success', 'Pengajuan berhasil dikirim untuk verifikasi.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
