<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pengajuan_anggarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_kerja_id')->constrained('unit_kerjas');
            $table->foreignId('program_anggaran_id')->constrained('program_anggarans');
            $table->foreignId('user_id')->constrained('users');
            $table->string('judul_usulan', 200);
            $table->text('keterangan')->nullable();
            $table->decimal('nominal_usulan', 14, 2);
            $table->enum('status', [
                'draft',
                'menunggu_verifikasi',
                'terverifikasi',
                'disetujui_pimpinan',
                'diajukan_ke_polrestabes',  // baru
                'dana_cair',
                'ditolak'
            ])->default('draft');
            $table->string('file_lampiran')->nullable();
            $table->string('nomor_referensi_astina')->nullable(); // baru: no. ref ASTINA
            $table->timestamp('diajukan_pada')->nullable();
            $table->timestamp('terverifikasi_pada')->nullable();
            $table->timestamp('acc_pimpinan_pada')->nullable();
            $table->timestamp('diajukan_polrestabes_pada')->nullable();
            $table->timestamp('dana_cair_pada')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_anggarans');
    }
};
