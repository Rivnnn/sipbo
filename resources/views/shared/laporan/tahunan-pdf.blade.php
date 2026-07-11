<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #1a1a1a;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
        }

        .header h3 {
            margin: 0;
            color: #0A1F44;
        }

        .header p {
            margin: 2px 0;
            font-size: 10px;
        }

        h4 {
            color: #0A1F44;
            margin-bottom: 2px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 5px 6px;
        }

        th {
            background-color: #0A1F44;
            color: #fff;
            text-align: left;
            font-size: 9px;
        }

        td {
            font-size: 9px;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        tfoot td {
            font-weight: bold;
            background-color: #F1F5F9;
        }

        .signature {
            display: flex;
            justify-content: space-between;
            margin-top: 60px;
        }

        .signature div {
            text-align: center;
            width: 45%;
            font-size: 11px;
        }

        .signature .line {
            margin-top: 50px;
            border-top: 1px solid #000;
            padding-top: 4px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h3>LAPORAN REALISASI ANGGARAN TAHUNAN</h3>
        <p>Polsek Bojongloa Kidul</p>
        <p>Tahun Anggaran: {{ $tahun }}</p>
    </div>

    <h4>1. Ringkasan Tahunan per Program</h4>
    <table>
        <thead>
            <tr>
                <th>Kode</th>
                <th>Nama Program</th>
                <th class="text-right">Pagu DIPA (Rp)</th>
                <th class="text-right">Total Dana Cair (Rp)</th>
                <th class="text-right">Total Distribusi (Rp)</th>
                <th class="text-right">Sisa Pagu (Rp)</th>
                <th class="text-center">% Terserap</th>
            </tr>
        </thead>
        <tbody>
            @php $totalPagu = 0; $totalCair = 0; $totalDistribusi = 0; $totalSisa = 0; @endphp
            @forelse($programs as $p)
            @php
            $totalPagu += $p['pagu_dipa'];
            $totalCair += $p['total_dana_cair'];
            $totalDistribusi += $p['total_distribusi'];
            $totalSisa += $p['sisa_pagu'];
            @endphp
            <tr>
                <td>{{ $p['kode_program'] }}</td>
                <td>{{ $p['nama_program'] }}</td>
                <td class="text-right">{{ number_format($p['pagu_dipa'], 2) }}</td>
                <td class="text-right">{{ number_format($p['total_dana_cair'], 2) }}</td>
                <td class="text-right">{{ number_format($p['total_distribusi'], 2) }}</td>
                <td class="text-right">{{ number_format($p['sisa_pagu'], 2) }}</td>
                <td class="text-center">{{ $p['persentase'] }}%</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center">Tidak ada program anggaran tahun {{ $tahun }}.</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2">TOTAL</td>
                <td class="text-right">{{ number_format($totalPagu, 2) }}</td>
                <td class="text-right">{{ number_format($totalCair, 2) }}</td>
                <td class="text-right">{{ number_format($totalDistribusi, 2) }}</td>
                <td class="text-right">{{ number_format($totalSisa, 2) }}</td>
                <td class="text-center">
                    {{ $totalPagu > 0 ? round($totalDistribusi / $totalPagu * 100, 2) : 0 }}%
                </td>
            </tr>
        </tfoot>
    </table>

    <div style="page-break-before: always;"></div>

    <h4>2. Rekap Distribusi per Bulan (Rp)</h4>
    <p style="font-size: 9px; margin-top: -6px;">Nominal kredit (distribusi ke unit) tiap bulan, per program.</p>
    <table>
        <thead>
            <tr>
                <th>Program</th>
                @foreach(range(1,12) as $b)
                <th class="text-right">{{ \Carbon\Carbon::createFromDate(2000, $b, 1)->translatedFormat('M') }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($programs as $p)
            <tr>
                <td>{{ $p['kode_program'] }}</td>
                @foreach(range(1,12) as $b)
                <td class="text-right">{{ number_format($p['bulanan'][$b]['kredit'], 0, ',', '.') }}</td>
                @endforeach
            </tr>
            @empty
            <tr>
                <td colspan="13" class="text-center">Tidak ada data.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div style="page-break-before: always;"></div>

    <h4>3. Rincian Transaksi Setahun</h4>
    <p style="font-size: 9px; margin-top: -6px;">Seluruh transaksi BKU tahun {{ $tahun }}, urut per program &amp; tanggal.</p>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Program</th>
                <th>Tanggal</th>
                <th>Uraian</th>
                <th class="text-center">Tipe</th>
                <th class="text-right">Debit (Rp)</th>
                <th class="text-right">Kredit (Rp)</th>
                <th class="text-right">Saldo (Rp)</th>
                <th>Input oleh</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transaksi as $t)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $t->programAnggaran->kode_program }}</td>
                <td>{{ $t->tanggal_transaksi->format('d-m-Y') }}</td>
                <td>{{ $t->uraian }}</td>
                <td class="text-center">{{ $t->debit > 0 ? 'DEBIT' : 'KREDIT' }}</td>
                <td class="text-right">{{ $t->debit > 0 ? number_format($t->debit, 2) : '-' }}</td>
                <td class="text-right">{{ $t->kredit > 0 ? number_format($t->kredit, 2) : '-' }}</td>
                <td class="text-right">{{ number_format($t->saldo, 2) }}</td>
                <td>{{ $t->inputBy->name }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center">Tidak ada transaksi pada tahun {{ $tahun }}.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="signature">
        <div>
            Mengetahui,<br>Pimpinan / Kapolsek
            <div class="line">( ............................... )</div>
        </div>
        <div>
            Dibuat oleh,<br>Admin / KASIUM
            <div class="line">( ............................... )</div>
        </div>
    </div>
</body>

</html>
