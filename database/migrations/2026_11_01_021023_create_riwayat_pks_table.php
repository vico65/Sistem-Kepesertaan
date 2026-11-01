<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * CreateRiwayatPksTable Migration
 *
 * Tujuan    : Membuat tabel riwayat_pks untuk mencatat histori perubahan data PKS.
 * Dibuat    : 2026-11-01
 * Perubahan : -
 */
return new class extends Migration
{
    /**
     * Jalankan migration — membuat tabel riwayat_pks di database.
     */
    public function up(): void
    {
        Schema::create('riwayat_pks', function (Blueprint $table) {
            // no_riwayat adalah auto-increment primary key standar
            $table->id('no_riwayat');

            // Referensi ke tabel pks — cascade delete agar riwayat ikut terhapus jika PKS dihapus
            $table->string('kode_pks', 50);
            $table->foreign('kode_pks')->references('kode_pks')->on('pks')->cascadeOnDelete();

            // Referensi ke staff yang melakukan perubahan
            $table->unsignedInteger('update_by');
            $table->foreign('update_by')->references('npp')->on('staffs');

            $table->string('kategori_perubahan', 99);

            // data_lama dan data_baru nullable karena saat insert pertama tidak ada nilai lama
            $table->string('data_lama', 99)->nullable();
            $table->string('data_baru', 99)->nullable();

            // Hanya butuh created_at, tidak butuh updated_at
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Rollback migration — hapus tabel riwayat_pks.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_pks');
    }
};
