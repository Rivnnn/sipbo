<?php
// app/Exports/BkuRingkasanSheet.php

namespace App\Exports;

use App\Models\{ProgramAnggaran, BukuKasUmum};
use Maatwebsite\Excel\Concerns\{
    FromArray,
    WithTitle,
    WithStyles,
    ShouldAutoSize
};
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BkuRingkasanSheet implements FromArray, WithTitle, WithStyles, ShouldAutoSize
{
    public function __construct(
        private int $programId,
        private int $bulan,
        private int $tahun
    ) {}

    public function array(): array
    {
        $program   = ProgramAnggaran::findOrFail($this->programId);
        $namaBulan = \Carbon\Carbon::createFromDate($this->tahun, $this->bulan, 1)
            ->translatedFormat('F Y');

        $debitPeriode  = BukuKasUmum::where('program_anggaran_id', $this->programId)
            ->whereYear('tanggal_transaksi', $this->tahun)
            ->whereMonth('tanggal_transaksi', $this->bulan)
            ->sum('debit');

        $kreditPeriode = BukuKasUmum::where('program_anggaran_id', $this->programId)
            ->whereYear('tanggal_transaksi', $this->tahun)
            ->whereMonth('tanggal_transaksi', $this->bulan)
            ->sum('kredit');

        return [
            ['RINGKASAN LAPORAN BKU'],
            ['Program: ' . $program->nama_program . ' (' . $program->kode_program . ')'],
            ['Periode: ' . $namaBulan],
            [],
            ['KOMPONEN', 'NILAI (Rp)'],
            ['Pagu DIPA (Tetap)', (float)$program->pagu_dipa],
            ['Total Dana Cair s.d. Sekarang', (float)$program->total_dana_cair],
            ['Sisa Pagu Belum Dicairkan', (float)$program->sisa_pagu],
            [],
            ['Dana Cair Periode Ini', (float)$debitPeriode],
            ['Total Distribusi Periode Ini', (float)$kreditPeriode],
            [],
            ['Total Distribusi s.d. Sekarang', (float)$program->total_distribusi],
            ['Saldo BKU Berjalan', (float)$program->saldo_berjalan],
            ['Persentase Realisasi', $program->persentase_realisasi . '%'],
        ];
    }

    public function title(): string
    {
        return 'Ringkasan';
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->mergeCells('A1:B1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(13);
        $sheet->getStyle('A5:B5')->getFont()->setBold(true);
        $sheet->getStyle('A5:B5')
            ->getFill()->setFillType('solid')
            ->getStartColor()->setRGB('2C3E50');
        $sheet->getStyle('A5:B5')
            ->getFont()->getColor()->setRGB('FFFFFF');

        return [];
    }
}
