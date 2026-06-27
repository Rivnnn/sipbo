<?php

namespace App\Exports;

use App\Models\BukuKasUmum;
use App\Models\ProgramAnggaran;
use Maatwebsite\Excel\Concerns\{FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles, ShouldAutoSize};
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class BkuExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles, ShouldAutoSize
{
    public function __construct(
        private int $programId,
        private int $bulan,
        private int $tahun
    ) {}

    public function collection()
    {
        return BukuKasUmum::where('program_anggaran_id', $this->programId)
            ->whereYear('tanggal_transaksi', $this->tahun)
            ->whereMonth('tanggal_transaksi', $this->bulan)
            ->orderBy('tanggal_transaksi')
            ->orderBy('id')
            ->get();
    }

    public function headings(): array
    {
        $program = ProgramAnggaran::find($this->programId);
        return [
            ["LAPORAN BUKU KAS UMUM"],
            ["Program: {$program->nama_program}"],
            ["Periode: " . \Carbon\Carbon::createFromDate($this->tahun, $this->bulan, 1)->translatedFormat('F Y')],
            [],
            ['Tanggal', 'Uraian', 'Debit (Rp)', 'Kredit (Rp)', 'Saldo (Rp)'],
        ];
    }

    public function map($row): array
    {
        return [
            $row->tanggal_transaksi->format('d-m-Y'),
            $row->uraian,
            $row->debit,
            $row->kredit,
            $row->saldo,
        ];
    }

    public function title(): string
    {
        return 'Buku Kas Umum';
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->mergeCells('A1:E1');
        $sheet->mergeCells('A2:E2');
        $sheet->mergeCells('A3:E3');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1:A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A5:E5')->getFont()->setBold(true);
        $sheet->getStyle('A5:E5')->getFill()->setFillType('solid')->getStartColor()->setRGB('0A1F44');
        $sheet->getStyle('A5:E5')->getFont()->getColor()->setRGB('FFFFFF');

        return [];
    }
}
