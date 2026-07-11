<?php
// app/Exports/TahunanBulananSheet.php

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

class TahunanBulananSheet implements
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
        $bulanNames = [];
        foreach (range(1, 12) as $b) {
            $bulanNames[] = \Carbon\Carbon::createFromDate(2000, $b, 1)->translatedFormat('F');
        }

        return array_merge(['Kode Program', 'Nama Program'], $bulanNames);
    }

    public function map($program): array
    {
        $bku = $this->bkuByProgram->get($program->id) ?? collect();

        $bulanan = [];
        foreach (range(1, 12) as $b) {
            $bulanan[] = (float) $bku
                ->filter(fn($t) => (int) $t->tanggal_transaksi->format('n') === $b)
                ->sum('kredit');
        }

        return array_merge([$program->kode_program, $program->nama_program], $bulanan);
    }

    public function title(): string
    {
        return 'Rekap Bulanan';
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->getStyle('A1:N1')->getFont()->setBold(true);
        $sheet->getStyle('A1:N1')
            ->getFill()->setFillType('solid')
            ->getStartColor()->setRGB('2C3E50');
        $sheet->getStyle('A1:N1')
            ->getFont()->getColor()->setRGB('FFFFFF');

        return [];
    }
}
