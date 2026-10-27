<?php

namespace Database\Seeders;

/**
 * StaffSeeder
 *
 * Tujuan    : Mengisi tabel staffs dengan 10 data dummy untuk kebutuhan
 *             development dan testing. Password semua staff adalah 'password123'.
 * Dibuat    : 2026-10-27
 * Perubahan : -
 */

use App\Models\Staff;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StaffSeeder extends Seeder
{
    /**
     * Masukkan 10 data staff dummy ke database.
     * NPP menggunakan format 6 digit sesuai spesifikasi.
     */
    public function run(): void
    {
        // Data dummy spesifik — password semua adalah 'password123'
        $staffs = [
            ['npp' => 100001, 'nama' => 'Budi Santoso',        'email' => 'budi.santoso@kepesertaan.id',     'nomor_hp' => '081234567890', 'status' => 'aktif'],
            ['npp' => 100002, 'nama' => 'Siti Rahayu',         'email' => 'siti.rahayu@kepesertaan.id',      'nomor_hp' => '082345678901', 'status' => 'aktif'],
            ['npp' => 100003, 'nama' => 'Ahmad Fauzi',         'email' => 'ahmad.fauzi@kepesertaan.id',      'nomor_hp' => '083456789012', 'status' => 'aktif'],
            ['npp' => 100004, 'nama' => 'Dewi Lestari',        'email' => 'dewi.lestari@kepesertaan.id',     'nomor_hp' => '084567890123', 'status' => 'aktif'],
            ['npp' => 100005, 'nama' => 'Rizki Pratama',       'email' => 'rizki.pratama@kepesertaan.id',    'nomor_hp' => '085678901234', 'status' => 'aktif'],
            ['npp' => 100006, 'nama' => 'Nur Hidayah',         'email' => 'nur.hidayah@kepesertaan.id',      'nomor_hp' => '086789012345', 'status' => 'nonaktif'],
            ['npp' => 100007, 'nama' => 'Eko Prasetyo',        'email' => 'eko.prasetyo@kepesertaan.id',     'nomor_hp' => '087890123456', 'status' => 'aktif'],
            ['npp' => 100008, 'nama' => 'Fitria Anggraeni',    'email' => 'fitria.anggraeni@kepesertaan.id', 'nomor_hp' => '088901234567', 'status' => 'nonaktif'],
            ['npp' => 100009, 'nama' => 'Hendra Wijaya',       'email' => 'hendra.wijaya@kepesertaan.id',    'nomor_hp' => '089012345678', 'status' => 'keluar'],
            ['npp' => 100010, 'nama' => 'Indah Permatasari',   'email' => 'indah.permatasari@kepesertaan.id', 'nomor_hp' => '081123456789', 'status' => 'aktif'],
        ];

        foreach ($staffs as $data) {
            Staff::create([
                ...$data,
                // Hash password sebelum disimpan — jangan pernah simpan plain text
                'password' => Hash::make('password123'),
            ]);
        }
    }
}
