<?php
// app/Exports/TahunanTransaksiSheet.php

namespace App\Exports;

use App\Models\{BukuKasUmum, ProgramAnggaran};
use Maatwebsite\Excel\Concerns\{
    FromCollection,
    WithHeadings,
    WithMapping,
    WithTitle,
    WithStyles,
    ShouldAutoSize
};
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TahunanTransaksiSheet implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithTitle,
    WithStyles,
    ShouldAutoSize
{
    public function __construct(private int $tahun) {}

    public function collection()
    {
        $programIds = ProgramAnggaran::where('tahun_anggaran', $this->tahun)->pluck('id');

        return BukuKasUmum::with(['programAnggaran', 'inputBy'])
            ->whereIn('program_anggaran_id', $programIds)
            ->whereYear('tanggal_transaksi', $this->tahun)
            ->orderBy('program_anggaran_id')
            ->orderBy('tanggal_transaksi')
            ->orderBy('id')
            ->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Kode Program',
            'Nama Program',
            'Tanggal',
            'Uraian',
            'Tipe',
            'Debit (Rp)',
            'Kredit (Rp)',
            'Saldo (Rp)',
            'Input oleh',
        ];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;
        return [
            $no,
            $row->programAnggaran->kode_program,
            $row->programAnggaran->nama_program,
            $row->tanggal_transaksi->format('d-m-Y'),
            $row->uraian,
            $row->debit > 0 ? 'DEBIT (Dana Cair)' : 'KREDIT (Distribusi)',
            $row->debit > 0 ? (float) $row->debit : 0,
            $row->kredit > 0 ? (float) $row->kredit : 0,
            (float) $row->saldo,
            $row->inputBy->name,
        ];
    }

    public function title(): string
    {
        return 'Rincian Transaksi';
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->getStyle('A1:J1')->getFont()->setBold(true);
        $sheet->getStyle('A1:J1')
            ->getFill()->setFillType('solid')
            ->getStartColor()->setRGB('2C3E50');
        $sheet->getStyle('A1:J1')
            ->getFont()->getColor()->setRGB('FFFFFF');

        return [];
    }
}
