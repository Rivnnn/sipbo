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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 6px 8px;
        }

        th {
            background-color: #0A1F44;
            color: #fff;
            text-align: left;
            font-size: 10px;
        }

        td {
            font-size: 10px;
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

        .progress-bg {
            background-color: #e5e7eb;
            width: 100%;
            height: 8px;
            border-radius: 4px;
        }

        .progress-fill {
            background-color: #1E3A8A;
            height: 8px;
            border-radius: 4px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h3>LAPORAN REALISASI ANGGARAN</h3>
        <p>Polsek Bojongloa Kidul</p>
        <p>Periode: {{ $namaBulan }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Kode</th>
                <th>Nama Program</th>
                <th class="text-right">Pagu DIPA (Rp)</th>
                <th class="text-right">Realisasi Bulan Ini (Rp)</th>
                <th class="text-right">Sisa Anggaran (Rp)</th>
                <th class="text-center">% Terserap</th>
            </tr>
        </thead>
        <tbody>
            @php $totalPagu = 0; $totalRealisasi = 0; $totalSisa = 0; @endphp
            @forelse($programs as $p)
            @php
            $totalPagu += $p['pagu_dipa'];
            $totalRealisasi += $p['realisasi_bulan_ini'];
            $totalSisa += $p['sisa_anggaran'];
            @endphp
            <tr>
                <td>{{ $p['kode_program'] }}</td>
                <td>{{ $p['nama_program'] }}</td>
                <td class="text-right">{{ number_format($p['pagu_dipa'], 2) }}</td>
                <td class="text-right">{{ number_format($p['realisasi_bulan_ini'], 2) }}</td>
                <td class="text-right">{{ number_format($p['sisa_anggaran'], 2) }}</td>
                <td class="text-center">{{ $p['persentase'] }}%</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">Tidak ada data program anggaran.</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2">TOTAL</td>
                <td class="text-right">{{ number_format($totalPagu, 2) }}</td>
                <td class="text-right">{{ number_format($totalRealisasi, 2) }}</td>
                <td class="text-right">{{ number_format($totalSisa, 2) }}</td>
                <td class="text-center">
                    {{ $totalPagu > 0 ? round((($totalPagu - $totalSisa) / $totalPagu) * 100, 2) : 0 }}%
                </td>
            </tr>
        </tfoot>
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