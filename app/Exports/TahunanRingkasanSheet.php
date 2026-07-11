<?php
// app/Exports/TahunanRingkasanSheet.php

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

class TahunanRingkasanSheet implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithTitle,
    WithStyles,
    ShouldAutoSize
{
    private \Illuminate\Support\Collection $bkuByProgram;

    public function __construct(private int $tahun) {}

    public function collection()
    {
        $programs = ProgramAnggaran::where('tahun_anggaran', $this->tahun)->get();

        $this->bkuByProgram = BukuKasUmum::whereIn('program_anggaran_id', $programs->pluck('id'))
            ->whereYear('tanggal_transaksi', $this->tahun)
            ->get()
            ->groupBy('program_anggaran_id');

        return $programs;
    }

    public function headings(): array
    {
        return [
            'Kode Program',
            'Nama Program',
            'Pagu DIPA',
            'Total Dana Cair',
            'Total Distribusi',
            'Sisa Pagu',
            '% Terserap',
        ];
    }

    public function map($program): array
    {
        $bku = $this->bkuByProgram->get($program->id) ?? collect();

        $totalDebit  = (float) $bku->sum('debit');
        $totalKredit = (float) $bku->sum('kredit');
        $paguDipa    = (float) $program->pagu_dipa;

        return [
            $program->kode_program,
            $program->nama_program,
            $paguDipa,
            $totalDebit,
            $totalKredit,
            $paguDipa - $totalDebit,
            ($paguDipa > 0 ? round($totalKredit / $paguDipa * 100, 2) : 0) . '%',
        ];
    }

    public function title(): string
    {
        return 'Ringkasan Tahunan';
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->getStyle('A1:G1')->getFont()->setBold(true);
        $sheet->getStyle('A1:G1')
            ->getFill()->setFillType('solid')
            ->getStartColor()->setRGB('2C3E50');
        $sheet->getStyle('A1:G1')
            ->getFont()->getColor()->setRGB('FFFFFF');

        return [];
    }
}
