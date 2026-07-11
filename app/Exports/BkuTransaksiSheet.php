<?php
// app/Exports/BkuTransaksiSheet.php

namespace App\Exports;

use App\Models\BukuKasUmum;
use Maatwebsite\Excel\Concerns\{
    FromCollection,
    WithHeadings,
    WithMapping,
    WithTitle,
    WithStyles,
    ShouldAutoSize
};
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BkuTransaksiSheet implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithTitle,
    WithStyles,
    ShouldAutoSize
{
    public function __construct(
        private int $programId,
        private int $bulan,
        private int $tahun
    ) {}

    public function collection()
    {
        return BukuKasUmum::with(['inputBy', 'pengajuanAnggaran.unitKerja'])
            ->where('program_anggaran_id', $this->programId)
            ->whereYear('tanggal_transaksi', $this->tahun)
            ->whereMonth('tanggal_transaksi', $this->bulan)
            ->orderBy('tanggal_transaksi')
            ->orderBy('id')
            ->get();
    }

    public function headings(): array
    {
        return [
            ['DETAIL TRANSAKSI BUKU KAS UMUM'],
            ['Periode: ' . \Carbon\Carbon::createFromDate($this->tahun, $this->bulan, 1)->translatedFormat('F Y')],
            [],
            [
                'No',
                'Tanggal',
                'Uraian',
                'Tipe',
                'Terkait Pengajuan',
                'Unit Kerja',
                'Debit (Rp)',
                'Kredit (Rp)',
                'Saldo (Rp)',
                'Input oleh'
            ],
        ];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;
        return [
            $no,
            $row->tanggal_transaksi->format('d-m-Y'),
            $row->uraian,
            $row->debit > 0 ? 'DEBIT (Dana Cair)' : 'KREDIT (Distribusi)',
            $row->pengajuanAnggaran?->judul_usulan ?? '-',
            $row->pengajuanAnggaran?->unitKerja?->nama_unit ?? '-',
            $row->debit > 0 ? (float)$row->debit : 0,
            $row->kredit > 0 ? (float)$row->kredit : 0,
            (float)$row->saldo,
            $row->inputBy->name,
        ];
    }

    public function title(): string
    {
        return 'Transaksi BKU';
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->mergeCells('A1:J1');
        $sheet->mergeCells('A2:J2');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(13);
        $sheet->getStyle('A4:J4')->getFont()->setBold(true);
        $sheet->getStyle('A4:J4')
            ->getFill()->setFillType('solid')
            ->getStartColor()->setRGB('2C3E50');
        $sheet->getStyle('A4:J4')
            ->getFont()->getColor()->setRGB('FFFFFF');

        return [];
    }
}
