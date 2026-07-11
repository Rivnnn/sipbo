<?php
// app/Http/Controllers/Shared/LaporanController.php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Models\{ProgramAnggaran, BukuKasUmum, PengajuanAnggaran};
use App\Exports\{BkuExport, RealisasiAnggaranExport, LaporanTahunanExport};
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $programs = ProgramAnggaran::where('tahun_anggaran', now()->year)->get();
        $bulan    = $request->input('bulan', now()->month);
        $tahun    = $request->input('tahun', now()->year);

        return view('shared.laporan.index', compact('programs', 'bulan', 'tahun'));
    }

    /*
    |--------------------------------------------------------------------------
    | PDF DETAIL PER PROGRAM
    | Menampilkan:
    | - Info program (pagu, total cair, distribusi, sisa)
    | - Daftar transaksi BKU lengkap (debit & kredit)
    | - Daftar pengajuan yang terkait dalam periode
    | - Signature block
    |--------------------------------------------------------------------------
    */
    public function exportPdf(Request $request, $programId)
    {
        $program = ProgramAnggaran::findOrFail($programId);
        $bulan   = $request->input('bulan', now()->month);
        $tahun   = $request->input('tahun', now()->year);

        // Semua transaksi BKU periode ini
        $transaksi = BukuKasUmum::with(['inputBy', 'pengajuanAnggaran.unitKerja'])
            ->where('program_anggaran_id', $programId)
            ->whereYear('tanggal_transaksi', $tahun)
            ->whereMonth('tanggal_transaksi', $bulan)
            ->orderBy('tanggal_transaksi')
            ->orderBy('id')
            ->get();

        // Saldo awal periode (dari transaksi terakhir bulan sebelumnya)
        $saldoAwal = BukuKasUmum::where('program_anggaran_id', $programId)
            ->where(
                'tanggal_transaksi',
                '<',
                \Carbon\Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth()
            )
            ->latest('tanggal_transaksi')
            ->latest('id')
            ->value('saldo') ?? 0;

        // Pengajuan yang dana cairnya masuk periode ini
        $pengajuanPeriode = PengajuanAnggaran::with(['user', 'unitKerja', 'verifier', 'approver'])
            ->where('program_anggaran_id', $programId)
            ->where('status', 'dana_cair')
            ->whereYear('dana_cair_pada', $tahun)
            ->whereMonth('dana_cair_pada', $bulan)
            ->get();

        // Summary kalkulasi
        $totalDebit  = $transaksi->sum('debit');
        $totalKredit = $transaksi->sum('kredit');
        $saldoAkhir  = $transaksi->last()?->saldo ?? $saldoAwal;
        $namaBulan   = \Carbon\Carbon::createFromDate($tahun, $bulan, 1)
            ->translatedFormat('F Y');

        $pdf = Pdf::loadView('shared.laporan.bku-pdf', compact(
            'program',
            'transaksi',
            'pengajuanPeriode',
            'totalDebit',
            'totalKredit',
            'saldoAwal',
            'saldoAkhir',
            'namaBulan'
        ))->setPaper('a4', 'portrait');

        return $pdf->download(
            "Laporan-BKU-{$program->kode_program}-{$bulan}-{$tahun}.pdf"
        );
    }

    /*
    |--------------------------------------------------------------------------
    | PDF REALISASI SEMUA PROGRAM
    |--------------------------------------------------------------------------
    */
    public function exportRealisasiPdf(Request $request)
    {
        $bulan     = $request->input('bulan', now()->month);
        $tahun     = $request->input('tahun', now()->year);
        $namaBulan = \Carbon\Carbon::createFromDate($tahun, $bulan, 1)
            ->translatedFormat('F Y');

        $programEntities = ProgramAnggaran::with('bukuKasUmums')->where('tahun_anggaran', $tahun)->get();

        $programs = $programEntities->map(function ($program) use ($bulan, $tahun) {
                $realisasiBulanIni = BukuKasUmum::where('program_anggaran_id', $program->id)
                    ->whereYear('tanggal_transaksi', $tahun)
                    ->whereMonth('tanggal_transaksi', $bulan)
                    ->sum('kredit');

                return [
                    'nama_program'        => $program->nama_program,
                    'kode_program'        => $program->kode_program,
                    'pagu_dipa'           => $program->pagu_dipa,
                    'total_dana_cair'     => $program->total_dana_cair,
                    'realisasi_bulan_ini' => $realisasiBulanIni,
                    'total_distribusi'    => $program->total_distribusi,
                    'saldo_berjalan'      => $program->saldo_berjalan,
                    'sisa_anggaran'       => $program->sisa_pagu,
                    'persentase'          => $program->persentase_realisasi,
                ];
            });

        // Rincian transaksi seluruh program dalam periode ini (bukan cuma angka akumulasi)
        $transaksi = BukuKasUmum::with(['programAnggaran', 'inputBy'])
            ->whereIn('program_anggaran_id', $programEntities->pluck('id'))
            ->whereYear('tanggal_transaksi', $tahun)
            ->whereMonth('tanggal_transaksi', $bulan)
            ->orderBy('program_anggaran_id')
            ->orderBy('tanggal_transaksi')
            ->orderBy('id')
            ->get();

        $pdf = Pdf::loadView(
            'shared.laporan.realisasi-pdf',
            compact('programs', 'namaBulan', 'transaksi')
        )
            ->setPaper('a4', 'landscape');

        return $pdf->download("Laporan-Realisasi-{$bulan}-{$tahun}.pdf");
    }

    public function exportExcel(Request $request, $programId)
    {
        $program = ProgramAnggaran::findOrFail($programId);
        $bulan   = $request->input('bulan', now()->month);
        $tahun   = $request->input('tahun', now()->year);

        return Excel::download(
            new BkuExport($programId, $bulan, $tahun),
            "BKU-{$program->kode_program}-{$bulan}-{$tahun}.xlsx"
        );
    }

    public function exportRealisasiExcel(Request $request)
    {
        $bulan = $request->input('bulan', now()->month);
        $tahun = $request->input('tahun', now()->year);

        return Excel::download(
            new RealisasiAnggaranExport($bulan, $tahun),
            "Realisasi-{$bulan}-{$tahun}.xlsx"
        );
    }

    /*
    |--------------------------------------------------------------------------
    | LAPORAN TAHUNAN (AKUMULASI 12 BULAN, PER PROGRAM)
    | Menampilkan:
    | - Ringkasan tahunan per program (pagu, total cair, distribusi, sisa, %)
    | - Rekap bulanan (Jan-Des) per program, untuk melihat tren
    | - Rincian seluruh transaksi setahun, dikelompokkan per program
    |
    | Catatan: totalnya dihitung eksplisit pakai whereYear(tanggal_transaksi),
    | BUKAN memakai accessor total_dana_cair/total_distribusi milik model
    | (yang menjumlahkan SEMUA baris BKU pada program itu tanpa filter
    | tanggal). Ini supaya laporan tahunan tetap akurat walau suatu saat ada
    | transaksi yang tanggalnya "nyasar" ke luar tahun_anggaran program itu.
    |--------------------------------------------------------------------------
    */
    private function hitungRekapTahunan(int $tahun)
    {
        $programEntities = ProgramAnggaran::where('tahun_anggaran', $tahun)->get();

        // SATU query untuk semua transaksi tahun ini lintas semua program,
        // bukan query terpisah per program di dalam loop (dulu: N query
        // untuk N program; sekarang selalu 1 query berapa pun jumlah program).
        $semuaBku = BukuKasUmum::whereIn('program_anggaran_id', $programEntities->pluck('id'))
            ->whereYear('tanggal_transaksi', $tahun)
            ->get(['program_anggaran_id', 'tanggal_transaksi', 'debit', 'kredit']);

        return $programEntities->map(function ($program) use ($semuaBku, $tahun) {
                $bku = $semuaBku->where('program_anggaran_id', $program->id);

                $totalDebit  = (float) $bku->sum('debit');
                $totalKredit = (float) $bku->sum('kredit');
                $paguDipa    = (float) $program->pagu_dipa;

                $bulanan = [];
                for ($b = 1; $b <= 12; $b++) {
                    $bkuBulan = $bku->filter(
                        fn($t) => (int) $t->tanggal_transaksi->format('n') === $b
                    );
                    $bulanan[$b] = [
                        'debit'  => (float) $bkuBulan->sum('debit'),
                        'kredit' => (float) $bkuBulan->sum('kredit'),
                    ];
                }

                return [
                    'id'               => $program->id,
                    'kode_program'     => $program->kode_program,
                    'nama_program'     => $program->nama_program,
                    'pagu_dipa'        => $paguDipa,
                    'total_dana_cair'  => $totalDebit,
                    'total_distribusi' => $totalKredit,
                    'sisa_pagu'        => $paguDipa - $totalDebit,
                    'persentase'       => $paguDipa > 0 ? round($totalKredit / $paguDipa * 100, 2) : 0,
                    'bulanan'          => $bulanan,
                ];
            });
    }

    public function exportTahunanPdf(Request $request)
    {
        $tahun = (int) $request->input('tahun', now()->year);

        $programs = $this->hitungRekapTahunan($tahun);

        $transaksi = BukuKasUmum::with(['programAnggaran', 'inputBy'])
            ->whereIn('program_anggaran_id', ProgramAnggaran::where('tahun_anggaran', $tahun)->pluck('id'))
            ->whereYear('tanggal_transaksi', $tahun)
            ->orderBy('program_anggaran_id')
            ->orderBy('tanggal_transaksi')
            ->orderBy('id')
            ->get();

        $pdf = Pdf::loadView(
            'shared.laporan.tahunan-pdf',
            compact('programs', 'tahun', 'transaksi')
        )->setPaper('a4', 'landscape');

        return $pdf->download("Laporan-Tahunan-{$tahun}.pdf");
    }

    public function exportTahunanExcel(Request $request)
    {
        $tahun = (int) $request->input('tahun', now()->year);

        return Excel::download(
            new LaporanTahunanExport($tahun),
            "Laporan-Tahunan-{$tahun}.xlsx"
        );
    }
}
