<?php

namespace App\Exports;

use App\Models\{ProgramAnggaran, BukuKasUmum};
use Maatwebsite\Excel\Concerns\{FromCollection, WithHeadings, WithMapping, ShouldAutoSize};

class RealisasiAnggaranExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public function __construct(private int $bulan, private int $tahun) {}

    public function collection()
    {
        return ProgramAnggaran::where('tahun_anggaran', $this->tahun)->get();
    }

    public function headings(): array
    {
        return ['Kode Program', 'Nama Program', 'Pagu DIPA', 'Realisasi Bulan Ini', 'Sisa Anggaran', '% Terserap'];
    }

    public function map($program): array
    {
        $realisasiBulanIni = BukuKasUmum::where('program_anggaran_id', $program->id)
            ->whereYear('tanggal_transaksi', $this->tahun)
            ->whereMonth('tanggal_transaksi', $this->bulan)
            ->sum('kredit');

        $sisa = $program->saldo_berjalan;
        $persen = $program->pagu_dipa > 0
            ? round((($program->pagu_dipa - $sisa) / $program->pagu_dipa) * 100, 2)
            : 0;

        return [
            $program->kode_program,
            $program->nama_program,
            $program->pagu_dipa,
            $realisasiBulanIni,
            $sisa,
            $persen . '%',
        ];
    }
}
