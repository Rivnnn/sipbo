<?php
// app/Exports/RealisasiAnggaranExport.php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class RealisasiAnggaranExport implements WithMultipleSheets
{
    public function __construct(private int $bulan, private int $tahun) {}

    public function sheets(): array
    {
        return [
            new RealisasiRingkasanSheet($this->bulan, $this->tahun),
            new RealisasiTransaksiSheet($this->bulan, $this->tahun),
        ];
    }
}
