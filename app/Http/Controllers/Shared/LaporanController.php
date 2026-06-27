<?php
// app/Http/Controllers/Shared/LaporanController.php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Models\{ProgramAnggaran, BukuKasUmum};
use App\Exports\{BkuExport, RealisasiAnggaranExport};
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $programs = ProgramAnggaran::where('tahun_anggaran', now()->year)->get();
        $bulan = $request->input('bulan', now()->month);
        $tahun = $request->input('tahun', now()->year);

        return view('shared.laporan.index', compact('programs', 'bulan', 'tahun'));
    }

    public function exportPdf(Request $request, $programId)
    {
        $program = ProgramAnggaran::findOrFail($programId);

        $bulan = $request->input('bulan', now()->month);
        $tahun = $request->input('tahun', now()->year);

        $transaksi = BukuKasUmum::where('program_anggaran_id', $programId)
            ->whereYear('tanggal_transaksi', $tahun)
            ->whereMonth('tanggal_transaksi', $bulan)
            ->orderBy('tanggal_transaksi')
            ->orderBy('id')
            ->get();

        $saldoAwal = BukuKasUmum::where('program_anggaran_id', $programId)
            ->where('tanggal_transaksi', '<', now()->createFromDate($tahun, $bulan, 1))
            ->latest('tanggal_transaksi')->latest('id')
            ->value('saldo') ?? $program->pagu_dipa;

        $totalDebit = $transaksi->sum('debit');
        $totalKredit = $transaksi->sum('kredit');
        $saldoAkhir = $transaksi->last()->saldo ?? $saldoAwal;

        $namaBulan = \Carbon\Carbon::createFromDate($tahun, $bulan, 1)->translatedFormat('F Y');

        $pdf = Pdf::loadView('shared.laporan.bku-pdf', compact(
            'program',
            'transaksi',
            'totalDebit',
            'totalKredit',
            'saldoAwal',
            'saldoAkhir',
            'namaBulan'
        ))->setPaper('a4', 'portrait');

        return $pdf->download("Laporan-BKU-{$program->kode_program}-{$bulan}-{$tahun}.pdf");
    }

    public function exportRealisasiPdf(Request $request)
    {
        $bulan = $request->input('bulan', now()->month);
        $tahun = $request->input('tahun', now()->year);
        $namaBulan = \Carbon\Carbon::createFromDate($tahun, $bulan, 1)->translatedFormat('F Y');

        $programs = ProgramAnggaran::where('tahun_anggaran', $tahun)->get()->map(function ($program) use ($bulan, $tahun) {
            $totalRealisasi = BukuKasUmum::where('program_anggaran_id', $program->id)
                ->whereYear('tanggal_transaksi', $tahun)
                ->whereMonth('tanggal_transaksi', $bulan)
                ->sum('kredit');

            return [
                'nama_program' => $program->nama_program,
                'kode_program' => $program->kode_program,
                'pagu_dipa' => $program->pagu_dipa,
                'realisasi_bulan_ini' => $totalRealisasi,
                'sisa_anggaran' => $program->saldo_berjalan,
                'persentase' => $program->pagu_dipa > 0
                    ? round((($program->pagu_dipa - $program->saldo_berjalan) / $program->pagu_dipa) * 100, 2)
                    : 0,
            ];
        });

        $pdf = Pdf::loadView('shared.laporan.realisasi-pdf', compact('programs', 'namaBulan'))
            ->setPaper('a4', 'landscape');

        return $pdf->download("Laporan-Realisasi-Anggaran-{$bulan}-{$tahun}.pdf");
    }

    public function exportExcel(Request $request, $programId)
    {
        $program = ProgramAnggaran::findOrFail($programId);
        $bulan = $request->input('bulan', now()->month);
        $tahun = $request->input('tahun', now()->year);

        return Excel::download(new BkuExport($programId, $bulan, $tahun), "BKU-{$program->kode_program}-{$bulan}-{$tahun}.xlsx");
    }

    public function exportRealisasiExcel(Request $request)
    {
        $bulan = $request->input('bulan', now()->month);
        $tahun = $request->input('tahun', now()->year);

        return Excel::download(new RealisasiAnggaranExport($bulan, $tahun), "Realisasi-Anggaran-{$bulan}-{$tahun}.xlsx");
    }
}
