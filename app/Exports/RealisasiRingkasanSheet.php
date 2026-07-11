<?php
// app/Exports/RealisasiRingkasanSheet.php

namespace App\Exports;

use App\Models\{ProgramAnggaran, BukuKasUmum};
use Maatwebsite\Excel\Concerns\{
    FromCollection,
    WithHeadings,
    WithMapping,
    WithTitle,
    WithStyles,
    ShouldAutoSize
};
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RealisasiRingkasanSheet implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithTitle,
    WithStyles,
    ShouldAutoSize
{
    private \Illuminate\Support\Collection $bkuByProgram;

    public function __construct(private int $bulan, private int $tahun) {}

    public function collection()
    {
        $programs = ProgramAnggaran::with('bukuKasUmums')->where('tahun_anggaran', $this->tahun)->get();

        // SATU query untuk semua transaksi bulan ini lintas semua program,
        // bukan query terpisah tiap kali map() dipanggil per baris Excel.
        $this->bkuByProgram = BukuKasUmum::whereIn('program_anggaran_id', $programs->pluck('id'))
            ->whereYear('tanggal_transaksi', $this->tahun)
            ->whereMonth('tanggal_transaksi', $this->bulan)
            ->get()
            ->groupBy('program_anggaran_id');

        return $programs;
    }

    public function headings(): array
    {
        return ['Kode Program', 'Nama Program', 'Pagu DIPA', 'Realisasi Bulan Ini', 'Sisa Anggaran', '% Terserap'];
    }

    public function map($program): array
    {
        $realisasiBulanIni = (float) ($this->bkuByProgram->get($program->id) ?? collect())->sum('kredit');

        return [
            $program->kode_program,
            $program->nama_program,
            (float) $program->pagu_dipa,
            $realisasiBulanIni,
            (float) $program->sisa_pagu,
            $program->persentase_realisasi . '%',
        ];
    }

    public function title(): string
    {
        return 'Ringkasan';
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->getStyle('A1:F1')->getFont()->setBold(true);
        $sheet->getStyle('A1:F1')
            ->getFill()->setFillType('solid')
            ->getStartColor()->setRGB('2C3E50');
        $sheet->getStyle('A1:F1')
            ->getFont()->getColor()->setRGB('FFFFFF');

        return [];
    }
}
