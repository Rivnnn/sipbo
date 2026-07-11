<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * kode_program sebelumnya unique secara global. Ini menghalangi
     * pembuatan pagu DIPA baru di tahun anggaran berikutnya dengan
     * kode program yang sama (mis. "BPK-002" untuk 2027), padahal
     * pagu DIPA memang didesain diatur ulang setiap tahun per program.
     *
     * Diganti menjadi unique gabungan (kode_program, tahun_anggaran).
     */
    public function up(): void
    {
        Schema::table('program_anggarans', function (Blueprint $table) {
            $table->dropUnique(['kode_program']);
            $table->unique(['kode_program', 'tahun_anggaran'], 'program_anggarans_kode_tahun_unique');
        });
    }

    public function down(): void
    {
        Schema::table('program_anggarans', function (Blueprint $table) {
            $table->dropUnique('program_anggarans_kode_tahun_unique');
            $table->unique('kode_program');
        });
    }
};
