<!-- resources/views/shared/laporan/bku-pdf.blade.php -->
<!DOCTYPE html>
<html>

<head>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #1a1a1a;
            margin: 0;
            padding: 0;
        }

        .header {
            text-align: center;
            margin-bottom: 12px;
        }

        .header h2 {
            margin: 0;
            font-size: 14px;
            text-transform: uppercase;
        }

        .header h3 {
            margin: 2px 0;
            font-size: 12px;
        }

        .header p {
            margin: 2px 0;
            font-size: 10px;
        }

        hr {
            border: none;
            border-top: 2px solid #000;
            margin: 6px 0;
        }

        hr.thin {
            border-top: 1px solid #aaa;
        }

        /* Info Program */
        .info-box {
            border: 1px solid #ccc;
            border-radius: 4px;
            padding: 8px 10px;
            margin-bottom: 12px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4px 16px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
        }

        .info-label {
            color: #555;
        }

        .info-value {
            font-weight: bold;
        }

        .info-value.gold {
            color: #8a6200;
        }

        .info-value.red {
            color: #c0392b;
        }

        .info-value.green {
            color: #1a7a3c;
        }

        /* Section title */
        .section-title {
            font-size: 11px;
            font-weight: bold;
            background: #2c3e50;
            color: #fff;
            padding: 4px 8px;
            margin: 10px 0 4px;
        }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        th {
            background: #34495e;
            color: #fff;
            padding: 5px 6px;
            text-align: left;
            font-size: 10px;
            font-weight: bold;
        }

        td {
            padding: 4px 6px;
            font-size: 10px;
            border-bottom: 1px solid #e0e0e0;
        }

        tr:nth-child(even) td {
            background: #f9f9f9;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        tfoot td {
            font-weight: bold;
            background: #ecf0f1;
            border-top: 2px solid #2c3e50;
            padding: 5px 6px;
        }

        /* Badge status */
        .badge {
            display: inline-block;
            padding: 1px 6px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: bold;
        }

        .badge-cair {
            background: #d5f5e3;
            color: #1a7a3c;
        }

        .badge-debit {
            background: #d6eaf8;
            color: #1a5276;
        }

        .badge-kredit {
            background: #fde8e4;
            color: #922b21;
        }

        /* Signature block */
        .signature {
            display: flex;
            justify-content: space-between;
            margin-top: 50px;
        }

        .sig-box {
            text-align: center;
            width: 44%;
        }

        .sig-box .title {
            font-weight: bold;
            margin-bottom: 4px;
        }

        .sig-box .line {
            margin-top: 55px;
            border-top: 1px solid #000;
            padding-top: 4px;
            font-size: 10px;
        }

        .page-break {
            page-break-before: always;
        }

        .no-data {
            text-align: center;
            color: #888;
            font-style: italic;
            padding: 12px;
        }
    </style>
</head>

<body>

    <!-- ══════════════ HEADER ══════════════ -->
    <div class="header">
        <h2>LAPORAN BUKU KAS UMUM</h2>
        <h3>Polsek Bojongloa Kidul — {{ $program->nama_program }}</h3>
        <p>Kode Program: {{ $program->kode_program }} &nbsp;|&nbsp; Periode: {{ $namaBulan }}</p>
        <p>Tahun Anggaran: {{ $program->tahun_anggaran }}</p>
    </div>
    <hr>

    <!-- ══════════════ RINGKASAN PROGRAM ══════════════ -->
    <div class="info-box">
        <div class="info-grid">
            <div>
                <div class="info-row">
                    <span class="info-label">Pagu DIPA (Tetap)</span>
                    <span class="info-value">Rp {{ number_format($program->pagu_dipa, 0, ',', '.') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Total Dana Cair s.d. Periode Ini</span>
                    <span class="info-value green">Rp {{ number_format($program->total_dana_cair, 0, ',', '.') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Saldo Awal Periode</span>
                    <span class="info-value">Rp {{ number_format($saldoAwal, 0, ',', '.') }}</span>
                </div>
            </div>
            <div>
                <div class="info-row">
                    <span class="info-label">Total Distribusi Periode Ini</span>
                    <span class="info-value red">Rp {{ number_format($totalKredit, 0, ',', '.') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Dana Cair Periode Ini</span>
                    <span class="info-value green">Rp {{ number_format($totalDebit, 0, ',', '.') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Saldo Akhir Periode</span>
                    <span class="info-value gold">Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ══════════════ BAGIAN 1: TRANSAKSI BKU ══════════════ -->
    <div class="section-title">I. DETAIL TRANSAKSI BUKU KAS UMUM</div>

    <table>
        <thead>
            <tr>
                <th style="width:5%">No</th>
                <th style="width:11%">Tanggal</th>
                <th>Uraian</th>
                <th style="width:13%">Terkait Pengajuan</th>
                <th style="width:10%">Unit</th>
                <th style="width:13%" class="text-right">Debit (Rp)</th>
                <th style="width:13%" class="text-right">Kredit (Rp)</th>
                <th style="width:13%" class="text-right">Saldo (Rp)</th>
                <th style="width:10%">Input oleh</th>
            </tr>
        </thead>
        <tbody>
            @if($saldoAwal > 0)
            <tr>
                <td class="text-center">-</td>
                <td>-</td>
                <td><em>Saldo Awal Periode</em></td>
                <td>-</td>
                <td>-</td>
                <td class="text-right">-</td>
                <td class="text-right">-</td>
                <td class="text-right"><strong>{{ number_format($saldoAwal, 0, ',', '.') }}</strong></td>
                <td>-</td>
            </tr>
            @endif
            @forelse($transaksi as $i => $t)
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td>{{ $t->tanggal_transaksi->format('d-m-Y') }}</td>
                <td>
                    {{ $t->uraian }}
                    @if($t->debit > 0)
                    <span class="badge badge-debit">DEBIT</span>
                    @else
                    <span class="badge badge-kredit">KREDIT</span>
                    @endif
                </td>
                <td>{{ $t->pengajuanAnggaran?->judul_usulan ?? '-' }}</td>
                <td>{{ $t->pengajuanAnggaran?->unitKerja?->kode_unit ?? '-' }}</td>
                <td class="text-right">
                    {{ $t->debit > 0 ? number_format($t->debit, 0, ',', '.') : '-' }}
                </td>
                <td class="text-right">
                    {{ $t->kredit > 0 ? number_format($t->kredit, 0, ',', '.') : '-' }}
                </td>
                <td class="text-right"><strong>{{ number_format($t->saldo, 0, ',', '.') }}</strong></td>
                <td>{{ $t->inputBy->name }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="no-data">Tidak ada transaksi pada periode ini.</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" class="text-right">TOTAL PERIODE INI</td>
                <td class="text-right">{{ number_format($totalDebit, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($totalKredit, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($saldoAkhir, 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <!-- ══════════════ BAGIAN 2: PENGAJUAN YANG DANA CAIRNYA MASUK PERIODE INI ══════════════ -->
    <div class="section-title">II. DAFTAR PENGAJUAN YANG DANA CAIR PADA PERIODE INI</div>

    <table>
        <thead>
            <tr>
                <th style="width:5%">No</th>
                <th>Judul Pengajuan</th>
                <th style="width:12%">Unit</th>
                <th style="width:12%">Pengaju</th>
                <th style="width:13%" class="text-right">Nominal (Rp)</th>
                <th style="width:13%">Tgl Dana Cair</th>
                <th style="width:12%">Diverifikasi</th>
                <th style="width:12%">Disetujui</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pengajuanPeriode as $i => $p)
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td>{{ $p->judul_usulan }}</td>
                <td>{{ $p->unitKerja->kode_unit }}</td>
                <td>{{ $p->user->name }}</td>
                <td class="text-right">{{ number_format($p->nominal_usulan, 0, ',', '.') }}</td>
                <td>{{ $p->dana_cair_pada?->format('d-m-Y') ?? '-' }}</td>
                <td>{{ $p->verifier?->name ?? '-' }}</td>
                <td>{{ $p->approver?->name ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="no-data">Tidak ada pengajuan yang dana cair pada periode ini.</td>
            </tr>
            @endforelse
        </tbody>
        @if($pengajuanPeriode->count() > 0)
        <tfoot>
            <tr>
                <td colspan="4" class="text-right">TOTAL DANA CAIR</td>
                <td class="text-right">{{ number_format($pengajuanPeriode->sum('nominal_usulan'), 0, ',', '.') }}</td>
                <td colspan="3"></td>
            </tr>
        </tfoot>
        @endif
    </table>

    <!-- ══════════════ BAGIAN 3: AUDIT TRAIL RINGKAS ══════════════ -->
    <div class="section-title">III. CATATAN PENTING</div>
    <table>
        <tbody>
            <tr>
                <td style="width:30%">Pagu DIPA Tahun {{ $program->tahun_anggaran }}</td>
                <td>Rp {{ number_format($program->pagu_dipa, 0, ',', '.') }} (Tetap)</td>
            </tr>
            <tr>
                <td>Total Dana Cair s.d. Akhir Periode</td>
                <td>Rp {{ number_format($program->total_dana_cair, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Total Distribusi s.d. Akhir Periode</td>
                <td>Rp {{ number_format($program->total_distribusi, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Sisa Pagu Belum Dicairkan</td>
                <td><strong>Rp {{ number_format($program->sisa_pagu, 0, ',', '.') }}</strong></td>
            </tr>
            <tr>
                <td>Saldo BKU Berjalan</td>
                <td><strong>Rp {{ number_format($program->saldo_berjalan, 0, ',', '.') }}</strong></td>
            </tr>
            <tr>
                <td>Persentase Realisasi</td>
                <td><strong>{{ $program->persentase_realisasi }}%</strong></td>
            </tr>
        </tbody>
    </table>

    <!-- ══════════════ SIGNATURE BLOCK ══════════════ -->
    <div class="signature">
        <div class="sig-box">
            <div class="title">Mengetahui,</div>
            <div>Pimpinan / Kapolsek</div>
            <div class="line">( ..................................... )</div>
        </div>
        <div class="sig-box">
            <div class="title">Dibuat oleh,</div>
            <div>KASIUM / Admin</div>
            <div class="line">( ..................................... )</div>
        </div>
    </div>

    <p style="font-size:9px;color:#888;text-align:center;margin-top:20px">
        Dicetak oleh sistem SIPBO pada {{ now()->format('d-m-Y H:i') }} WIB
        &nbsp;|&nbsp; Polsek Bojongloa Kidul
    </p>

</body>

</html>