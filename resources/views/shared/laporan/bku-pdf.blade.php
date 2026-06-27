<!-- resources/views/laporan/bku-pdf.blade.php -->
<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #1a1a1a;
        }

        .header {
            text-align: center;
            margin-bottom: 5px;
        }

        .header h3 {
            margin: 0;
            color: #0A1F44;
        }

        .header p {
            margin: 2px 0;
            font-size: 11px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
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
            font-size: 11px;
        }

        td {
            font-size: 11px;
        }

        .text-right {
            text-align: right;
        }

        .saldo-awal {
            background-color: #F1F5F9;
            font-weight: bold;
        }

        tfoot td {
            font-weight: bold;
            background-color: #F1F5F9;
        }

        .signature {
            display: flex;
            justify-content: space-between;
            margin-top: 70px;
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
        <h3>LAPORAN BUKU KAS UMUM</h3>
        <p>Program: {{ $program->nama_program }} ({{ $program->kode_program }})</p>
        <p>Periode: {{ $namaBulan }}</p>
        <p>Polsek Bojongloa Kidul</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:12%">Tanggal</th>
                <th>Uraian</th>
                <th class="text-right" style="width:15%">Debit (Rp)</th>
                <th class="text-right" style="width:15%">Kredit (Rp)</th>
                <th class="text-right" style="width:15%">Saldo (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <tr class="saldo-awal">
                <td colspan="4">Saldo Awal Periode</td>
                <td class="text-right">{{ number_format($saldoAwal, 2) }}</td>
            </tr>
            @forelse($transaksi as $t)
            <tr>
                <td>{{ $t->tanggal_transaksi->format('d-m-Y') }}</td>
                <td>{{ $t->uraian }}</td>
                <td class="text-right">{{ $t->debit > 0 ? number_format($t->debit, 2) : '-' }}</td>
                <td class="text-right">{{ $t->kredit > 0 ? number_format($t->kredit, 2) : '-' }}</td>
                <td class="text-right">{{ number_format($t->saldo, 2) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align:center; color:#888;">Tidak ada transaksi pada periode ini.</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2">TOTAL & SALDO AKHIR</td>
                <td class="text-right">{{ number_format($totalDebit, 2) }}</td>
                <td class="text-right">{{ number_format($totalKredit, 2) }}</td>
                <td class="text-right">{{ number_format($saldoAkhir, 2) }}</td>
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