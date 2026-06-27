<?php

namespace Database\Seeders;

use Database\Seeders\RolePermissionSeeder;
use App\Models\{User, UnitKerja, ProgramAnggaran, PengajuanAnggaran};
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

        $this->command->info('Seeder selesai. Login dengan: staf.intelkam / kasium / kapolsek (password: password)');
    }
}
