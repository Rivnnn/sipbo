<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\{Role, Permission};

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'dashboard.lihat',
            'pengajuan.create',
            'pengajuan.edit-draft',
            'pengajuan.kirim',
            'pengajuan.verifikasi',
            'pengajuan.setujui',
            'pengajuan.tolak',
            'pengajuan.update-eksternal',
            'bku.input',
            'bku.lihat',
            'laporan.export-pdf',
            'laporan.export-excel',
            'arsip.lihat',
            'settings.users',
            'settings.units',
            'settings.permissions',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // STAF UNIT
        $staf = Role::firstOrCreate(['name' => 'staf_unit', 'guard_name' => 'web']);
        $staf->syncPermissions([
            'dashboard.lihat',
            'pengajuan.create',
            'pengajuan.edit-draft',
            'pengajuan.kirim',
            'arsip.lihat',
        ]);

        // KASIUM
        $kasium = Role::firstOrCreate(['name' => 'kasium', 'guard_name' => 'web']);
        $kasium->syncPermissions([
            'dashboard.lihat',
            'pengajuan.verifikasi',
            'pengajuan.tolak',
            'pengajuan.update-eksternal',
            'bku.input',
            'bku.lihat',
            'laporan.export-pdf',
            'laporan.export-excel',
            'arsip.lihat',
            'settings.users',
            'settings.units',
            'settings.permissions',   // ← ini yang menyebabkan 403
        ]);

        // PIMPINAN
        $pimpinan = Role::firstOrCreate(['name' => 'pimpinan', 'guard_name' => 'web']);
        $pimpinan->syncPermissions([
            'dashboard.lihat',
            'pengajuan.setujui',
            'pengajuan.tolak',
            'bku.lihat',
            'laporan.export-pdf',
            'laporan.export-excel',
            'arsip.lihat',
        ]);
    }
}
