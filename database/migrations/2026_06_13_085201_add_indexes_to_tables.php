<?php
// database/migrations/xxxx_add_indexes_to_tables.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengajuan_anggarans', function (Blueprint $table) {
            $table->index('status');
            $table->index('user_id');
            $table->index('program_anggaran_id');
            $table->index('dana_cair_pada');
        });

        Schema::table('buku_kas_umums', function (Blueprint $table) {
            $table->index('program_anggaran_id');
            $table->index('tanggal_transaksi');
        });

        Schema::table('audit_trails', function (Blueprint $table) {
            $table->index('pengajuan_anggaran_id');
            $table->index('waktu_eksekusi');
        });
    }

    public function down(): void
    {
        Schema::table('pengajuan_anggarans', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['program_anggaran_id']);
            $table->dropIndex(['dana_cair_pada']);
        });

        Schema::table('buku_kas_umums', function (Blueprint $table) {
            $table->dropIndex(['program_anggaran_id']);
            $table->dropIndex(['tanggal_transaksi']);
        });

        Schema::table('audit_trails', function (Blueprint $table) {
            $table->dropIndex(['pengajuan_anggaran_id']);
            $table->dropIndex(['waktu_eksekusi']);
        });
    }
};
