<?php

use App\Models\Pks;
use App\Models\RiwayatPks;
use App\Models\Staff;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Siapkan data dummy staff untuk foreign key npp_ro dan update_by
    Staff::create([
        'npp' => 10982,
        'nama' => 'Staff Test',
        'email' => 'staff.test@kepesertaan.id',
        'nomor_hp' => '081234567890',
        'password' => 'password123',
        'status' => 'aktif',
    ]);
});

test('POST /api/pks berhasil menambahkan data PBPU Pemda dan mencatat riwayat', function () {
    $payload = [
        'kode_pks' => '16710001',
        'nama_pks' => 'PKS PBPU Pemda Kota Palembang 2026',
        'jenis_pks' => 'PBPU Pemda',
        'npp_ro' => 10982,
        'jumlah_anggaran' => 2500000000,
        'tmt_nk' => '2026-01-01',
        'tat_nk' => '2026-12-31',
        'tmt_rk' => '2026-01-01',
        'tat_rk' => '2026-12-31',
        'status_pks' => 'Aktif',
    ];

    $response = $this->postJson('/api/pks', $payload);

    $response->assertStatus(201)
        ->assertJson([
            'message' => 'Data PKS berhasil ditambahkan',
            'data' => [
                'kode_pks' => '16710001',
                'nama_pks' => 'PKS PBPU Pemda Kota Palembang 2026',
                'jenis_pks' => 'PBPU Pemda',
                'status_pks' => 'Aktif',
            ],
        ]);

    $this->assertDatabaseHas('pks', [
        'kode_pks' => '16710001',
        'nama_pks' => 'PKS PBPU Pemda Kota Palembang 2026',
    ]);

    $this->assertDatabaseHas('riwayat_pks', [
        'kode_pks' => '16710001',
        'update_by' => 10982,
        'kategori_perubahan' => 'Data baru ditambahkan',
    ]);
});

test('POST /api/pks berhasil menambahkan PBPU Kolektif tanpa anggaran dan NK', function () {
    $payload = [
        'kode_pks' => 'PKS-KOL-2026-042',
        'nama_pks' => 'PKS Kolektif PT Pusri Palembang',
        'jenis_pks' => 'PBPU Kolektif',
        'npp_ro' => 10982,
        'tmt_rk' => '2026-05-01',
        'tat_rk' => '2027-04-30',
        'status_pks' => 'Aktif',
    ];

    $response = $this->postJson('/api/pks', $payload);

    $response->assertStatus(201)
        ->assertJson([
            'message' => 'Data PKS berhasil ditambahkan',
            'data' => [
                'kode_pks' => 'PKS-KOL-2026-042',
                'nama_pks' => 'PKS Kolektif PT Pusri Palembang',
                'jenis_pks' => 'PBPU Kolektif',
                'status_pks' => 'Aktif',
            ],
        ]);
});

test('POST /api/pks gagal jika PBPU Pemda tidak mengisi anggaran dan NK', function () {
    $payload = [
        'kode_pks' => '16710099',
        'nama_pks' => 'PKS PBPU Pemda Test',
        'jenis_pks' => 'PBPU Pemda',
        'npp_ro' => 10982,
        'tmt_rk' => '2026-01-01',
        'tat_rk' => '2026-12-31',
    ];

    $response = $this->postJson('/api/pks', $payload);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['jumlah_anggaran', 'tmt_nk', 'tat_nk'])
        ->assertJsonFragment([
            'message' => 'Data PKS gagal ditambahkan',
        ]);
});

test('POST /api/pks gagal jika kode_pks duplikat', function () {
    Pks::create([
        'kode_pks' => '16710001',
        'nama_pks' => 'PKS Eksisting',
        'jenis_pks' => 'PBPU Kolektif',
        'npp_ro' => 10982,
        'tmt_rk' => '2026-01-01',
        'tat_rk' => '2026-12-31',
        'status_pks' => 'Aktif',
    ]);

    $payload = [
        'kode_pks' => '16710001',
        'nama_pks' => 'PKS Duplikat',
        'jenis_pks' => 'PBPU Kolektif',
        'npp_ro' => 10982,
        'tmt_rk' => '2026-01-01',
        'tat_rk' => '2026-12-31',
    ];

    $response = $this->postJson('/api/pks', $payload);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['kode_pks']);
});

test('GET /api/pks mengembalikan daftar semua pks', function () {
    Pks::create([
        'kode_pks' => '16710001',
        'nama_pks' => 'PKS 1',
        'jenis_pks' => 'PBPU Kolektif',
        'tmt_rk' => '2026-01-01',
        'tat_rk' => '2026-12-31',
    ]);

    $response = $this->getJson('/api/pks');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'message',
            'data' => [
                '*' => ['kode_pks', 'nama_pks', 'jenis_pks', 'status_pks'],
            ],
        ]);
});

test('GET /api/pks/{kode_pks} mengembalikan detail data pks beserta riwayat', function () {
    $pks = Pks::create([
        'kode_pks' => '16710001',
        'nama_pks' => 'PKS 1',
        'jenis_pks' => 'PBPU Kolektif',
        'npp_ro' => 10982,
        'tmt_rk' => '2026-01-01',
        'tat_rk' => '2026-12-31',
    ]);

    RiwayatPks::create([
        'kode_pks' => $pks->kode_pks,
        'update_by' => 10982,
        'kategori_perubahan' => 'Data baru ditambahkan',
    ]);

    $response = $this->getJson('/api/pks/16710001');

    $response->assertStatus(200)
        ->assertJsonPath('data.kode_pks', '16710001')
        ->assertJsonCount(1, 'data.riwayat');
});

test('GET /api/pks/{kode_pks} return 404 jika tidak ditemukan', function () {
    $response = $this->getJson('/api/pks/NONEXISTENT');

    $response->assertStatus(404)
        ->assertJson([
            'message' => 'Data PKS tidak ditemukan',
        ]);
});

test('PUT /api/pks/{kode_pks} berhasil memperbarui data dan mencatat riwayat perubahan', function () {
    $pks = Pks::create([
        'kode_pks' => '16710001',
        'nama_pks' => 'PKS PBPU Pemda Lama',
        'jenis_pks' => 'PBPU Pemda',
        'npp_ro' => 10982,
        'jumlah_anggaran' => 1000000,
        'tmt_nk' => '2026-01-01',
        'tat_nk' => '2026-12-31',
        'tmt_rk' => '2026-01-01',
        'tat_rk' => '2026-12-31',
        'status_pks' => 'Aktif',
    ]);

    $payload = [
        'nama_pks' => 'PKS PBPU Pemda Revisi',
        'jenis_pks' => 'PBPU Pemda',
        'npp_ro' => 10982,
        'jumlah_anggaran' => 2000000,
        'tmt_nk' => '2026-01-01',
        'tat_nk' => '2026-12-31',
        'tmt_rk' => '2026-01-01',
        'tat_rk' => '2026-12-31',
        'status_pks' => 'Aktif',
    ];

    $response = $this->putJson('/api/pks/16710001', $payload);

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Data PKS berhasil diperbarui',
            'data' => [
                'kode_pks' => '16710001',
                'nama_pks' => 'PKS PBPU Pemda Revisi',
            ],
        ]);

    $this->assertDatabaseHas('riwayat_pks', [
        'kode_pks' => '16710001',
        'kategori_perubahan' => 'Ubah nama_pks',
        'data_lama' => 'PKS PBPU Pemda Lama',
        'data_baru' => 'PKS PBPU Pemda Revisi',
    ]);
});

test('DELETE /api/pks/{kode_pks} berhasil menghapus data pks', function () {
    Pks::create([
        'kode_pks' => '16710001',
        'nama_pks' => 'PKS To Delete',
        'jenis_pks' => 'PBPU Kolektif',
        'tmt_rk' => '2026-01-01',
        'tat_rk' => '2026-12-31',
    ]);

    $response = $this->deleteJson('/api/pks/16710001');

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Data PKS berhasil dihapus',
        ]);

    $this->assertDatabaseMissing('pks', ['kode_pks' => '16710001']);
});

test('GET /api/pks/{kode_pks}/riwayat mengembalikan daftar riwayat pks', function () {
    $pks = Pks::create([
        'kode_pks' => '16710001',
        'nama_pks' => 'PKS Riwayat Test',
        'jenis_pks' => 'PBPU Kolektif',
        'tmt_rk' => '2026-01-01',
        'tat_rk' => '2026-12-31',
    ]);

    RiwayatPks::create([
        'kode_pks' => $pks->kode_pks,
        'update_by' => 10982,
        'kategori_perubahan' => 'Data baru ditambahkan',
    ]);

    $response = $this->getJson('/api/pks/16710001/riwayat');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'message',
            'data' => [
                '*' => ['no_riwayat', 'kode_pks', 'update_by', 'kategori_perubahan', 'created_at'],
            ],
        ]);
});
