<?php
// app/Exports/BkuExport.php

namespace App\Exports;

use App\Models\{BukuKasUmum, ProgramAnggaran};
use Maatwebsite\Excel\Concerns\{
    FromCollection,
    WithHeadings,
    WithMapping,
    WithTitle,
    WithStyles,
    ShouldAutoSize,
    WithMultipleSheets
};

class BkuExport implements WithMultipleSheets
{
    public function __construct(
        private int $programId,
        private int $bulan,
        private int $tahun
    ) {}

    public function sheets(): array
    {
        return [
            new BkuTransaksiSheet($this->programId, $this->bulan, $this->tahun),
            new BkuPengajuanSheet($this->programId, $this->bulan, $this->tahun),
            new BkuRingkasanSheet($this->programId, $this->bulan, $this->tahun),
        ];
    }
}
