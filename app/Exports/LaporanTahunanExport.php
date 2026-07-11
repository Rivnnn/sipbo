<?php
// app/Exports/LaporanTahunanExport.php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class LaporanTahunanExport implements WithMultipleSheets
{
    public function __construct(private int $tahun) {}

    public function sheets(): array
    {
        return [
            new TahunanRingkasanSheet($this->tahun),
            new TahunanBulananSheet($this->tahun),
            new TahunanTransaksiSheet($this->tahun),
        ];
    }
}
