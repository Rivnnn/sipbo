<?php

namespace Database\Seeders;

use Database\Seeders\RolePermissionSeeder;
use App\Models\{User, UnitKerja, ProgramAnggaran, PengajuanAnggaran, BukuKasUmum};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RolePermissionSeeder::class);

        // ===== UNIT KERJA =====
        $unitIntelkam = UnitKerja::create(['nama_unit' => 'Unit Intelkam', 'kode_unit' => 'INTELKAM']);
        $unitLantas = UnitKerja::create(['nama_unit' => 'Unit Lalu Lintas', 'kode_unit' => 'LANTAS']);
        $unitSium = UnitKerja::create(['nama_unit' => 'Seksi Umum', 'kode_unit' => 'SIUM']);

        // ===== USERS =====
        $staf = User::create([
            'username' => 'staf',
            'name' => 'Bripka Andi Saputra',
            'password' => Hash::make('staf123'),
            'unit_kerja_id' => $unitIntelkam->id,
        ]);
        $staf->assignRole('staf_unit');

        $kasium = User::create([
            'username' => 'kasium',
            'name' => 'Acep Rohimat, S.H.',
            'password' => Hash::make('kasium123'),
            'unit_kerja_id' => $unitSium->id,
        ]);
        $kasium->assignRole('kasium');

        $pimpinan = User::create([
            'username' => 'kapolsek',
            'name' => 'AKP Budi Santoso',
            'password' => Hash::make('kapolsek123'),
            'unit_kerja_id' => $unitSium->id,
        ]);
        $pimpinan->assignRole('pimpinan');

        // ===== PROGRAM ANGGARAN (DIPA) =====
        $program1 = ProgramAnggaran::create([
            'nama_program' => 'Belanja Operasional Perkantoran',
            'kode_program' => 'BOP-001',
            'pagu_dipa' => 50000000,
            'tahun_anggaran' => now()->year,
        ]);

        $program2 = ProgramAnggaran::create([
            'nama_program' => 'Belanja Pemeliharaan Kendaraan',
            'kode_program' => 'BPK-002',
            'pagu_dipa' => 25000000,
            'tahun_anggaran' => now()->year,
        ]);

        // ===== CONTOH PENGAJUAN (berbagai status untuk testing UI) =====
        PengajuanAnggaran::create([
            'unit_kerja_id' => $unitIntelkam->id,
            'program_anggaran_id' => $program1->id,
            'user_id' => $staf->id,
            'judul_usulan' => 'ATK & Konsumsi Rapat Bulan Juni',
            'keterangan' => 'Kebutuhan rutin operasional bulanan unit Intelkam.',
            'nominal_usulan' => 1500000,
            'status' => 'draft',
        ]);

        PengajuanAnggaran::create([
            'unit_kerja_id' => $unitLantas->id,
            'program_anggaran_id' => $program2->id,
            'user_id' => $staf->id,
            'judul_usulan' => 'Servis Berkala Kendaraan Patroli',
            'keterangan' => 'Servis rutin 3 kendaraan dinas.',
            'nominal_usulan' => 3000000,
            'status' => 'menunggu_verifikasi',
            'diajukan_pada' => now()->subDays(2),
        ]);

        PengajuanAnggaran::create([
            'unit_kerja_id' => $unitIntelkam->id,
            'program_anggaran_id' => $program1->id,
            'user_id' => $staf->id,
            'judul_usulan' => 'Pengadaan Tinta Printer',
            'nominal_usulan' => 750000,
            'status' => 'terverifikasi',
            'diajukan_pada' => now()->subDays(5),
            'terverifikasi_pada' => now()->subDays(4),
            'verified_by' => $kasium->id,
        ]);

        // ===== CONTOH PENGAJUAN YANG SUDAH DANA CAIR =====
        // PENTING: pengajuan berstatus 'dana_cair' HARUS punya baris debit
        // yang matching di buku_kas_umums. Di alur aplikasi nyata ini
        // dijamin oleh EksternalController::danaCair() (status + debit
        // dibuat atomically dalam satu DB transaction). Karena seeder ini
        // memakai Model::create() langsung (bypass controller/service),
        // baris BKU-nya harus dibuat manual di sini juga - kalau lupa,
        // saldo_berjalan program akan 0 padahal status sudah "dana_cair",
        // persis bug yang pernah terjadi.
        $pengajuanCair = PengajuanAnggaran::create([
            'unit_kerja_id' => $unitLantas->id,
            'program_anggaran_id' => $program2->id,
            'user_id' => $staf->id,
            'judul_usulan' => 'Servis Berkala Kendaraan Patroli - Batch 1',
            'keterangan' => 'Servis rutin kendaraan dinas batch pertama.',
            'nominal_usulan' => 3000000,
            'status' => 'dana_cair',
            'nomor_referensi_astina' => 'ASTINA/2026/000123',
            'diajukan_pada' => now()->subDays(10),
            'terverifikasi_pada' => now()->subDays(9),
            'verified_by' => $kasium->id,
            'acc_pimpinan_pada' => now()->subDays(8),
            'approved_by' => $pimpinan->id,
            'diajukan_polrestabes_pada' => now()->subDays(6),
            'dana_cair_pada' => now()->subDays(3),
        ]);

        // Baris DEBIT: dana masuk dari Polrestabes sebesar nominal_usulan
        BukuKasUmum::create([
            'pengajuan_anggaran_id' => $pengajuanCair->id,
            'program_anggaran_id'   => $program2->id,
            'tanggal_transaksi'     => now()->subDays(3)->toDateString(),
            'uraian'                => 'Dana cair - ' . $pengajuanCair->judul_usulan
                . ' (Ref ASTINA: ' . $pengajuanCair->nomor_referensi_astina . ')',
            'debit'                 => 3000000,
            'kredit'                => 0,
            'saldo'                 => 3000000,
            'input_by'              => $kasium->id,
        ]);

        // Baris KREDIT: sebagian dana didistribusikan ke Unit Lantas
        BukuKasUmum::create([
            'pengajuan_anggaran_id' => $pengajuanCair->id,
            'program_anggaran_id'   => $program2->id,
            'tanggal_transaksi'     => now()->subDays(2)->toDateString(),
            'uraian'                => 'Distribusi dana - ' . $pengajuanCair->judul_usulan,
            'debit'                 => 0,
            'kredit'                => 2000000,
            'saldo'                 => 1000000,
            'input_by'              => $kasium->id,
        ]);

        $this->command->info('Seeder selesai. Login dengan: staf.intelkam / kasium / kapolsek (password: password)');
    }
}
