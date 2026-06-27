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

        $programs = ProgramAnggaran::where('tahun_anggaran', $tahun)->get()->map(function ($program) {
            $realisasiBulanIni = BukuKasUmum::where('program_anggaran_id', $program->id)
                ->whereMonth('tanggal_transaksi', now()->month)
                ->whereYear('tanggal_transaksi', now()->year)
                ->sum('kredit');

            $totalRealisasi = BukuKasUmum::where('program_anggaran_id', $program->id)->sum('kredit');
            $saldoBerjalan = $program->saldo_berjalan;

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

        $chartLabels = [];
        $chartData = [];
        for ($m = 1; $m <= 12; $m++) {
            $chartLabels[] = \Carbon\Carbon::createFromDate($tahun, $m, 1)->translatedFormat('M');
            $chartData[] = (float) BukuKasUmum::whereYear('tanggal_transaksi', $tahun)
                ->whereMonth('tanggal_transaksi', $m)
                ->sum('kredit');
        }

        return view('shared.dashboard.index', compact('programs', 'stats', 'recent', 'chartLabels', 'chartData'));
    }
}
