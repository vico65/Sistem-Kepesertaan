<?php

use App\Models\Staff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

test('staff dapat login dengan npp dan password yang valid', function () {
    Staff::create([
        'npp' => 100001,
        'nama' => 'Budi Santoso',
        'email' => 'budi.santoso@kepesertaan.id',
        'nomor_hp' => '081234567890',
        'password' => Hash::make('password123'),
        'status' => 'aktif',
    ]);

    $response = $this->postJson('/api/users/login', [
        'npp' => 100001,
        'password' => 'password123',
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'status' => 200,
            'message' => 'Login berhasil',
        ]);
});

test('login gagal jika password salah', function () {
    Staff::create([
        'npp' => 100001,
        'nama' => 'Budi Santoso',
        'email' => 'budi.santoso@kepesertaan.id',
        'nomor_hp' => '081234567890',
        'password' => Hash::make('password123'),
        'status' => 'aktif',
    ]);

    $response = $this->postJson('/api/users/login', [
        'npp' => 100001,
        'password' => 'salah123',
    ]);

    $response->assertStatus(400)
        ->assertJson([
            'status' => 400,
            'message' => 'Login gagal',
        ]);
});

test('login gagal jika npp tidak terdaftar', function () {
    $response = $this->postJson('/api/users/login', [
        'npp' => 999999,
        'password' => 'password123',
    ]);

    $response->assertStatus(400)
        ->assertJson([
            'status' => 400,
            'message' => 'Login gagal',
        ]);
});

test('login gagal jika status staff nonaktif atau keluar', function () {
    Staff::create([
        'npp' => 100006,
        'nama' => 'Nur Hidayah',
        'email' => 'nur.hidayah@kepesertaan.id',
        'nomor_hp' => '086789012345',
        'password' => Hash::make('password123'),
        'status' => 'nonaktif',
    ]);

    $response = $this->postJson('/api/users/login', [
        'npp' => 100006,
        'password' => 'password123',
    ]);

    $response->assertStatus(400)
        ->assertJson([
            'status' => 400,
            'message' => 'Login gagal',
        ]);
});
