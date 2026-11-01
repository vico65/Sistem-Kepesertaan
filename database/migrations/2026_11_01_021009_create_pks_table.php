<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * CreatePksTable Migration
 *
 * Tujuan    : Membuat tabel pks untuk menyimpan dokumen perjanjian kerja sama.
 * Dibuat    : 2026-11-01
 * Perubahan : -
 */
return new class extends Migration
{
    /**
     * Jalankan migration — membuat tabel pks di database.
     */
    public function up(): void
    {
        Schema::create('pks', function (Blueprint $table) {
            // kode_pks adalah primary key berupa string (bukan auto-increment)
            $table->string('kode_pks', 50)->primary();

            $table->string('nama_pks', 255);

            $table->enum('jenis_pks', ['PBPU Pemda', 'PBPU Kolektif']);

            // npp_ro nullable karena bisa belum ada RO yang ditugaskan
            // unsignedInteger karena mengacu ke staffs.npp yang bertipe unsignedInteger
            $table->unsignedInteger('npp_ro')->nullable();
            $table->foreign('npp_ro')->references('npp')->on('staffs')->nullOnDelete();

            // jumlah_anggaran nullable karena PBPU Kolektif boleh tidak mengisinya
            $table->bigInteger('jumlah_anggaran')->nullable();

            // NK = Nota Kesepahaman, nullable karena PBPU Kolektif tidak wajib
            $table->date('tmt_nk')->nullable();
            $table->date('tat_nk')->nullable();

            // RK = Rencana Kerja, wajib untuk semua jenis PKS
            $table->date('tmt_rk');
            $table->date('tat_rk');

            // Default 'Aktif' agar saat insert tidak perlu selalu kirim field ini
            $table->enum('status_pks', ['Aktif', 'Tidak Aktif'])->default('Aktif');
        });
    }

    /**
     * Rollback migration — hapus tabel pks.
     */
    public function down(): void
    {
        Schema::dropIfExists('pks');
    }
};
