<?php

namespace Database\Seeders;

/**
 * PksSeeder
 *
 * Tujuan    : Mengisi tabel pks dan riwayat_pks dengan 10 data dummy
 *             untuk keperluan development dan testing.
 * Dibuat    : 2026-11-01
 * Perubahan : -
 */

use App\Models\Pks;
use App\Models\RiwayatPks;
use Illuminate\Database\Seeder;

class PksSeeder extends Seeder
{
    /**
     * Jalankan database seeds untuk PKS dan riwayat awal.
     */
    public function run(): void
    {
        $dataPks = [
            [
                'kode_pks' => '16710001',
                'nama_pks' => 'PKS PBPU Pemda Kota Palembang 2026',
                'jenis_pks' => 'PBPU Pemda',
                'npp_ro' => 100001,
                'jumlah_anggaran' => 2500000000,
                'tmt_nk' => '2026-01-01',
                'tat_nk' => '2026-12-31',
                'tmt_rk' => '2026-01-01',
                'tat_rk' => '2026-12-31',
                'status_pks' => 'Aktif',
            ],
            [
                'kode_pks' => '16710002',
                'nama_pks' => 'PKS PBPU Pemda Kabupaten Banyuasin 2026',
                'jenis_pks' => 'PBPU Pemda',
                'npp_ro' => 100002,
                'jumlah_anggaran' => 1800000000,
                'tmt_nk' => '2026-01-01',
                'tat_nk' => '2026-12-31',
                'tmt_rk' => '2026-01-01',
                'tat_rk' => '2026-12-31',
                'status_pks' => 'Aktif',
            ],
            [
                'kode_pks' => '16710003',
                'nama_pks' => 'PKS PBPU Pemda Kota Prabumulih 2026',
                'jenis_pks' => 'PBPU Pemda',
                'npp_ro' => 100003,
                'jumlah_anggaran' => 950000000,
                'tmt_nk' => '2026-03-01',
                'tat_nk' => '2026-12-31',
                'tmt_rk' => '2026-03-01',
                'tat_rk' => '2026-12-31',
                'status_pks' => 'Aktif',
            ],
            [
                'kode_pks' => 'PKS-KOL-2026-001',
                'nama_pks' => 'PKS Kolektif PT Pusri Palembang',
                'jenis_pks' => 'PBPU Kolektif',
                'npp_ro' => 100004,
                'jumlah_anggaran' => null,
                'tmt_nk' => null,
                'tat_nk' => null,
                'tmt_rk' => '2026-05-01',
                'tat_rk' => '2027-04-30',
                'status_pks' => 'Aktif',
            ],
            [
                'kode_pks' => 'PKS-KOL-2026-002',
                'nama_pks' => 'PKS Kolektif PT Pertamina RU III',
                'jenis_pks' => 'PBPU Kolektif',
                'npp_ro' => 100005,
                'jumlah_anggaran' => null,
                'tmt_nk' => null,
                'tat_nk' => null,
                'tmt_rk' => '2026-06-01',
                'tat_rk' => '2027-05-31',
                'status_pks' => 'Aktif',
            ],
            [
                'kode_pks' => 'PKS-KOL-2026-003',
                'nama_pks' => 'PKS Kolektif PT Bukit Asam Tbk',
                'jenis_pks' => 'PBPU Kolektif',
                'npp_ro' => 100006,
                'jumlah_anggaran' => null,
                'tmt_nk' => null,
                'tat_nk' => null,
                'tmt_rk' => '2026-04-01',
                'tat_rk' => '2027-03-31',
                'status_pks' => 'Aktif',
            ],
            [
                'kode_pks' => '16710004',
                'nama_pks' => 'PKS PBPU Pemda Kabupaten OKI 2026',
                'jenis_pks' => 'PBPU Pemda',
                'npp_ro' => 100007,
                'jumlah_anggaran' => 1200000000,
                'tmt_nk' => '2026-02-01',
                'tat_nk' => '2026-12-31',
                'tmt_rk' => '2026-02-01',
                'tat_rk' => '2026-12-31',
                'status_pks' => 'Aktif',
            ],
            [
                'kode_pks' => 'PKS-KOL-2026-004',
                'nama_pks' => 'PKS Kolektif PT PLN Sumbagsel',
                'jenis_pks' => 'PBPU Kolektif',
                'npp_ro' => 100008,
                'jumlah_anggaran' => null,
                'tmt_nk' => null,
                'tat_nk' => null,
                'tmt_rk' => '2026-07-01',
                'tat_rk' => '2027-06-30',
                'status_pks' => 'Tidak Aktif',
            ],
            [
                'kode_pks' => '16710005',
                'nama_pks' => 'PKS PBPU Pemda Kota Lubuklinggau 2025',
                'jenis_pks' => 'PBPU Pemda',
                'npp_ro' => 100009,
                'jumlah_anggaran' => 750000000,
                'tmt_nk' => '2025-01-01',
                'tat_nk' => '2025-12-31',
                'tmt_rk' => '2025-01-01',
                'tat_rk' => '2025-12-31',
                'status_pks' => 'Tidak Aktif',
            ],
            [
                'kode_pks' => 'PKS-KOL-2025-099',
                'nama_pks' => 'PKS Kolektif Universitas Sriwijaya',
                'jenis_pks' => 'PBPU Kolektif',
                'npp_ro' => 100010,
                'jumlah_anggaran' => null,
                'tmt_nk' => null,
                'tat_nk' => null,
                'tmt_rk' => '2025-08-01',
                'tat_rk' => '2026-07-31',
                'status_pks' => 'Aktif',
            ],
        ];

        foreach ($dataPks as $item) {
            $pks = Pks::updateOrCreate(
                ['kode_pks' => $item['kode_pks']],
                $item
            );

            // Buat record riwayat awal jika belum ada
            RiwayatPks::firstOrCreate(
                [
                    'kode_pks' => $pks->kode_pks,
                    'kategori_perubahan' => 'Data baru ditambahkan',
                ],
                [
                    'update_by' => $item['npp_ro'],
                    'data_lama' => null,
                    'data_baru' => null,
                ]
            );
        }
    }
}
