<?php
// app/Http/Controllers/Shared/DashboardController.php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Models\{ProgramAnggaran, PengajuanAnggaran, BukuKasUmum};
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $tahun = now()->year;
        $bulanIni = now()->month;

        $programEntities = ProgramAnggaran::where('tahun_anggaran', $tahun)->get();
        $programIds = $programEntities->pluck('id');

        // SATU query untuk semua transaksi program tahun ini, bukan 2-3 query
        // TERPISAH per program seperti sebelumnya (dulu: makin banyak program,
        // makin banyak query — sekarang selalu 1 query berapa pun jumlah program).
        $semuaTransaksi = BukuKasUmum::whereIn('program_anggaran_id', $programIds)
            ->get(['id', 'program_anggaran_id', 'tanggal_transaksi', 'debit', 'kredit', 'saldo']);

        $programs = $programEntities->map(function ($program) use ($semuaTransaksi, $bulanIni, $tahun) {
            $rows = $semuaTransaksi->where('program_anggaran_id', $program->id);

            $realisasiBulanIni = (float) $rows->filter(
                fn($r) => (int) $r->tanggal_transaksi->format('n') === $bulanIni
                    && (int) $r->tanggal_transaksi->format('Y') === $tahun
            )->sum('kredit');

            $totalRealisasi = (float) $rows->sum('kredit');
            $last = $rows->sortBy('id')->sortBy('tanggal_transaksi')->last();
            $saldoBerjalan = $last ? (float) $last->saldo : 0;

            return [
                'id' => $program->id,
                'nama_program' => $program->nama_program,
                'kode_program' => $program->kode_program,
                'pagu_dipa' => $program->pagu_dipa,
                'realisasi_bulan_ini' => $realisasiBulanIni,
                'total_realisasi' => $totalRealisasi,
                'sisa_anggaran' => $saldoBerjalan,
                'persentase_terserap' => $program->pagu_dipa > 0
                    ? round(($totalRealisasi / $program->pagu_dipa) * 100, 2)
                    : 0,
            ];
        });

        $stats = [];

        if ($user->hasRole('staf_unit')) {
            $stats = [
                'draft' => PengajuanAnggaran::where('user_id', $user->id)->where('status', 'draft')->count(),
                'menunggu_verifikasi' => PengajuanAnggaran::where('user_id', $user->id)->where('status', 'menunggu_verifikasi')->count(),
                'disetujui' => PengajuanAnggaran::where('user_id', $user->id)->whereIn('status', ['disetujui_pimpinan', 'diajukan_ke_polrestabes', 'dana_cair'])->count(),
                'ditolak' => PengajuanAnggaran::where('user_id', $user->id)->where('status', 'ditolak')->count(),
            ];
        } elseif ($user->hasRole('kasium')) {
            $stats = [
                'menunggu_verifikasi' => PengajuanAnggaran::where('status', 'menunggu_verifikasi')->count(),
                'menunggu_diajukan' => PengajuanAnggaran::where('status', 'disetujui_pimpinan')->count(),
                'diajukan_polrestabes' => PengajuanAnggaran::where('status', 'diajukan_ke_polrestabes')->count(),
                'dana_cair_bulan_ini' => PengajuanAnggaran::where('status', 'dana_cair')
                    ->whereMonth('dana_cair_pada', now()->month)->count(),
            ];
        } elseif ($user->hasRole('pimpinan')) {
            $stats = [
                'menunggu_approval' => PengajuanAnggaran::where('status', 'terverifikasi')->count(),
                'disetujui_bulan_ini' => PengajuanAnggaran::where('status', '!=', 'draft')
                    ->whereMonth('acc_pimpinan_pada', now()->month)
                    ->whereYear('acc_pimpinan_pada', now()->year)->count(),
                'ditolak_bulan_ini' => PengajuanAnggaran::where('status', 'ditolak')
                    ->whereMonth('updated_at', now()->month)->count(),
            ];
        }

        $recentQuery = PengajuanAnggaran::with(['programAnggaran', 'unitKerja', 'user'])->latest('updated_at');

        if ($user->hasRole('staf_unit')) {
            $recentQuery->where('user_id', $user->id);
        }

        $recent = $recentQuery->take(5)->get();

        $monthlySums = BukuKasUmum::whereYear('tanggal_transaksi', $tahun)
            ->selectRaw('MONTH(tanggal_transaksi) as bulan, SUM(kredit) as total')
            ->groupBy('bulan')
            ->pluck('total', 'bulan');

        $chartLabels = [];
        $chartData = [];
        for ($m = 1; $m <= 12; $m++) {
            $chartLabels[] = \Carbon\Carbon::createFromDate($tahun, $m, 1)->translatedFormat('M');
            $chartData[] = (float) ($monthlySums[$m] ?? 0);
        }

        return view('shared.dashboard.index', compact('programs', 'stats', 'recent', 'chartLabels', 'chartData'));
    }
}
