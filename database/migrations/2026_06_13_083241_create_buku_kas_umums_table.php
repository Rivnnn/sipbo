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
        Schema::create('buku_kas_umums', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_anggaran_id')->nullable()->constrained('pengajuan_anggarans');
            $table->foreignId('program_anggaran_id')->constrained('program_anggarans');
            $table->date('tanggal_transaksi');
            $table->string('uraian', 200);
            $table->decimal('debit', 14, 2)->default(0);
            $table->decimal('kredit', 14, 2)->default(0);
            $table->decimal('saldo', 14, 2); // dihitung otomatis
            $table->foreignId('input_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buku_kas_umums');
    }
};
