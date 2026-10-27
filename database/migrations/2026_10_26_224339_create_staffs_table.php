<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * CreateStaffsTable Migration
 *
 * Tujuan    : Membuat tabel staffs untuk menyimpan data pegawai/staff
 * Dibuat    : 2026-10-27
 * Perubahan : -
 */
return new class extends Migration
{
    /**
     * Jalankan migration — membuat tabel staffs di database.
     */
    public function up(): void
    {
        Schema::create('staffs', function (Blueprint $table) {
            // NPP = Nomor Pokok Pegawai, digunakan sebagai primary key
            // unsignedInteger karena nilainya selalu positif, length 6 digit
            $table->unsignedInteger('npp')->primary();

            $table->string('nama');

            // Email harus unik karena digunakan untuk identifikasi staff
            $table->string('email')->unique();

            $table->string('nomor_hp');

            // Password di-hash sebelum disimpan (lihat seeder)
            $table->string('password');

            // Status default 'aktif' ketika staff baru dibuat
            $table->enum('status', ['aktif', 'nonaktif', 'keluar'])->default('aktif');

            $table->timestamps(); // created_at dan updated_at otomatis diisi Laravel
        });
    }

    /**
     * Rollback migration — hapus tabel staffs.
     */
    public function down(): void
    {
        Schema::dropIfExists('staffs');
    }
};
