<?php

use App\Models\Staff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

test('halaman dashboard dapat diakses dan menampilkan konten layout', function () {
    $response = $this->get(route('dashboard'));

    $response->assertSuccessful()
        ->assertSee('Dashboard')
        ->assertSee('Selamat datang di Sistem Kepesertaan.')
        ->assertSee('Kepesertaan')
        ->assertSee('KC Palembang');
});

test('halaman list pegawai dapat diakses dan menampilkan daftar staff', function () {
    Staff::create([
        'npp' => 100001,
        'nama' => 'Budi Santoso',
        'email' => 'budi.santoso@kepesertaan.id',
        'nomor_hp' => '081234567890',
        'password' => Hash::make('password123'),
        'status' => 'aktif',
    ]);

    Staff::create([
        'npp' => 100006,
        'nama' => 'Nur Hidayah',
        'email' => 'nur.hidayah@kepesertaan.id',
        'nomor_hp' => '086789012345',
        'password' => Hash::make('password123'),
        'status' => 'nonaktif',
    ]);

    Staff::create([
        'npp' => 100009,
        'nama' => 'Hendra Wijaya',
        'email' => 'hendra.wijaya@kepesertaan.id',
        'nomor_hp' => '089012345678',
        'password' => Hash::make('password123'),
        'status' => 'keluar',
    ]);

    $response = $this->get(route('staff.index'));

    $response->assertSuccessful()
        ->assertSee('Daftar Staf')
        ->assertSee('Manajemen data pegawai dan penanggung jawab PKS')
        ->assertSee('100001')
        ->assertSee('Budi Santoso')
        ->assertSee('100006')
        ->assertSee('Nur Hidayah')
        ->assertSee('100009')
        ->assertSee('Hendra Wijaya')
        ->assertSee('Aktif')
        ->assertSee('Nonaktif/Mutasi')
        ->assertSee('Keluar')
        ->assertSee('modal_tambah_staf')
        ->assertSee('modal_edit_staf');
});
