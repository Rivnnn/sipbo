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
        Schema::create('program_anggarans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_program', 150);
            $table->string('kode_program', 30)->unique();
            $table->decimal('pagu_dipa', 14, 2); // saldo awal/total alokasi
            $table->year('tahun_anggaran');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('program_anggarans');
    }
};
