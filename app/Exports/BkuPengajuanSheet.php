<?php
// app/Exports/BkuPengajuanSheet.php

namespace App\Exports;

use App\Models\PengajuanAnggaran;
use Maatwebsite\Excel\Concerns\{
    FromCollection,
    WithHeadings,
    WithMapping,
    WithTitle,
    WithStyles,
    ShouldAutoSize
};
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BkuPengajuanSheet implements
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
        return PengajuanAnggaran::with(['user', 'unitKerja', 'verifier', 'approver'])
            ->where('program_anggaran_id', $this->programId)
            ->where('status', 'dana_cair')
            ->whereYear('dana_cair_pada', $this->tahun)
            ->whereMonth('dana_cair_pada', $this->bulan)
            ->get();
    }

    public function headings(): array
    {
        return [
            ['DAFTAR PENGAJUAN YANG DANA CAIR PADA PERIODE INI'],
            [],
            [
                'No',
                'Judul Pengajuan',
                'Unit Kerja',
                'Pengaju',
                'Nominal (Rp)',
                'Tgl Diajukan',
                'Tgl Terverifikasi',
                'Diverifikasi oleh',
                'Tgl ACC',
                'Disetujui oleh',
                'Tgl Dana Cair',
                'No. Ref ASTINA'
            ],
        ];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;
        return [
            $no,
            $row->judul_usulan,
            $row->unitKerja->nama_unit,
            $row->user->name,
            (float)$row->nominal_usulan,
            $row->diajukan_pada?->format('d-m-Y') ?? '-',
            $row->terverifikasi_pada?->format('d-m-Y') ?? '-',
            $row->verifier?->name ?? '-',
            $row->acc_pimpinan_pada?->format('d-m-Y') ?? '-',
            $row->approver?->name ?? '-',
            $row->dana_cair_pada?->format('d-m-Y') ?? '-',
            $row->nomor_referensi_astina ?? '-',
        ];
    }

    public function title(): string
    {
        return 'Daftar Pengajuan';
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->mergeCells('A1:L1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A3:L3')->getFont()->setBold(true);
        $sheet->getStyle('A3:L3')
            ->getFill()->setFillType('solid')
            ->getStartColor()->setRGB('2C3E50');
        $sheet->getStyle('A3:L3')
            ->getFont()->getColor()->setRGB('FFFFFF');

        return [];
    }
}
