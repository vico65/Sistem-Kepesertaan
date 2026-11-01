# Issue: Implementasi Modul PKS (Perjanjian Kerja Sama)

> [!IMPORTANT]
> Dokumen ini adalah panduan implementasi lengkap untuk fitur Modul PKS.
> Baca seluruh dokumen sebelum mulai coding. Ikuti **tahapan secara berurutan** — jangan loncat ke tahap berikutnya sebelum tahap sebelumnya selesai dan diuji.

---

## Konteks Proyek

Proyek ini adalah sistem kepesertaan berbasis **Laravel 13** dengan PHP 8.3.

Struktur penting yang sudah ada dan **harus dipahami sebelum mulai**:

| File / Folder | Keterangan |
|---|---|
| `app/Models/Staff.php` | Model Staff — primary key-nya adalah `npp` (integer, non-increment) |
| `database/migrations/2026_10_26_224339_create_staffs_table.php` | Struktur tabel `staffs` |
| `database/seeders/StaffSeeder.php` | Data dummy staff (NPP: 100001–100010) |
| `routes/api.php` | Tempat mendaftarkan route API baru |
| `app/Http/Controllers/Api/` | Folder untuk semua API Controller |

> [!NOTE]
> **Konvensi komentar:** Setiap file PHP yang dibuat atau diubah **wajib** memiliki PHPDoc block di bagian atas yang menjelaskan: Tujuan, Dibuat (tanggal), dan Perubahan. Lihat file yang sudah ada sebagai contoh.

---

## Gambaran Umum Fitur

Fitur ini menambahkan manajemen data **PKS (Perjanjian Kerja Sama)** beserta riwayat perubahannya ke dalam sistem. Terdiri dari:

1. **Tabel `pks`** — menyimpan data PKS
2. **Tabel `riwayat_pks`** — menyimpan log setiap perubahan pada data PKS
3. **API CRUD** untuk kedua tabel tersebut

---

## Skema Database

### Tabel 1: `pks`

| Kolom | Tipe | Constraint | Keterangan |
|---|---|---|---|
| `kode_pks` | `VARCHAR(50)` | NOT NULL, PRIMARY KEY | Kode unik PKS (bukan auto-increment) |
| `nama_pks` | `VARCHAR(255)` | NOT NULL | Nama/judul dokumen PKS |
| `jenis_pks` | `ENUM('PBPU Pemda', 'PBPU Kolektif')` | NOT NULL | Jenis PKS |
| `npp_ro` | `UNSIGNED INTEGER(7)` | NULLABLE, FK → `staffs.npp` | NPP Staff Relationship Officer yang bertanggung jawab |
| `jumlah_anggaran` | `BIGINT` | NULLABLE | Nilai anggaran PKS (dalam Rupiah) |
| `tmt_nk` | `DATE` | NULLABLE | Tanggal Mulai Berlaku Nota Kesepahaman |
| `tat_nk` | `DATE` | NULLABLE | Tanggal Akhir Berlaku Nota Kesepahaman |
| `tmt_rk` | `DATE` | NOT NULL | Tanggal Mulai Berlaku Rencana Kerja |
| `tat_rk` | `DATE` | NOT NULL | Tanggal Akhir Berlaku Rencana Kerja |
| `status_pks` | `ENUM('Aktif', 'Tidak Aktif')` | NOT NULL, DEFAULT `'Aktif'` | Status keaktifan dokumen PKS |

> [!NOTE]
> `kode_pks` bisa berupa format angka saja (`16710001`) atau format string (`PKS-KOL-2026-042`). Gunakan `string` di migration, bukan `integer`.

### Tabel 2: `riwayat_pks`

| Kolom | Tipe | Constraint | Keterangan |
|---|---|---|---|
| `no_riwayat` | `BIGINT` | NOT NULL, PRIMARY KEY, AUTO-INCREMENT | ID unik riwayat |
| `kode_pks` | `VARCHAR(50)` | NOT NULL, FK → `pks.kode_pks` | Referensi ke PKS yang diubah |
| `update_by` | `UNSIGNED INTEGER` | NOT NULL, FK → `staffs.npp` | NPP Staff yang melakukan perubahan |
| `kategori_perubahan` | `VARCHAR(99)` | NOT NULL | Deskripsi jenis perubahan (contoh: `"Data baru ditambahkan"`, `"Nama PKS diubah"`) |
| `data_lama` | `VARCHAR(99)` | NULLABLE | Nilai data sebelum diubah |
| `data_baru` | `VARCHAR(99)` | NULLABLE | Nilai data setelah diubah |
| `created_at` | `TIMESTAMP` | NOT NULL, DEFAULT CURRENT_TIMESTAMP | Waktu perubahan dicatat |

> [!IMPORTANT]
> **Aturan Bisnis Kritis:** Setiap kali data PKS **ditambahkan**, sistem **wajib otomatis** mencatat satu baris ke tabel `riwayat_pks` dengan `kategori_perubahan = "Data baru ditambahkan"`.

---

## API Endpoint

### 1. `POST /api/pks` — Tambah Data PKS Baru

**Validasi (Form Request):**

| Field | Aturan Validasi |
|---|---|
| `kode_pks` | required, string, max:50, unik di tabel `pks` |
| `nama_pks` | required, string, max:255 |
| `jenis_pks` | required, harus salah satu dari: `PBPU Pemda`, `PBPU Kolektif` |
| `npp_ro` | nullable, integer, harus ada di tabel `staffs` (exists) |
| `jumlah_anggaran` | nullable, integer, min:0. **Wajib diisi jika `jenis_pks` = `PBPU Pemda`** |
| `tmt_nk` | nullable, format date `Y-m-d`. **Wajib diisi jika `jenis_pks` = `PBPU Pemda`** |
| `tat_nk` | nullable, format date `Y-m-d`, harus setelah atau sama dengan `tmt_nk`. **Wajib diisi jika `jenis_pks` = `PBPU Pemda`** |
| `tmt_rk` | required, format date `Y-m-d` |
| `tat_rk` | required, format date `Y-m-d`, harus setelah atau sama dengan `tmt_rk` |
| `status_pks` | nullable, harus salah satu dari: `Aktif`, `Tidak Aktif`. Default: `Aktif` |

**Contoh Request Body (PBPU Pemda):**
```json
{
    "kode_pks": "16710001",
    "nama_pks": "PKS PBPU Pemda Kota Palembang 2026",
    "jenis_pks": "PBPU Pemda",
    "npp_ro": "10982",
    "jumlah_anggaran": 2500000000,
    "tmt_nk": "2026-01-01",
    "tat_nk": "2026-12-31",
    "tmt_rk": "2026-01-01",
    "tat_rk": "2026-12-31",
    "status_pks": "Aktif"
}
```

**Contoh Request Body (PBPU Kolektif — Anggaran & NK boleh kosong):**
```json
{
    "kode_pks": "PKS-KOL-2026-042",
    "nama_pks": "PKS Kolektif PT Pusri Palembang",
    "jenis_pks": "PBPU Kolektif",
    "npp_ro": "10982",
    "tmt_rk": "2026-05-01",
    "tat_rk": "2027-04-30",
    "status_pks": "Aktif"
}
```

**Response Sukses (HTTP 201):**
```json
{
    "message": "Data PKS berhasil ditambahkan",
    "data": {
        "kode_pks": "16710001",
        "nama_pks": "PKS PBPU Pemda Kota Palembang 2026",
        "jenis_pks": "PBPU Pemda",
        "status_pks": "Aktif"
    }
}
```

**Response Error Validasi (HTTP 422):**
```json
{
    "message": "Data PKS gagal ditambahkan",
    "errors": {
        "tmt_nk": [
            "Kolom tmt_nk wajib diisi jika jenis_pks adalah PBPU Pemda."
        ]
    }
}
```

**Response Error Server (HTTP 500):**
```json
{
    "message": "Data PKS gagal ditambahkan",
    "errors": "Pesan error teknis dari server"
}
```

---

### 2. `GET /api/pks` — Ambil Semua Data PKS

**Response Sukses (HTTP 200):**
```json
{
    "message": "Data PKS berhasil diambil",
    "data": [
        {
            "kode_pks": "16710001",
            "nama_pks": "PKS PBPU Pemda Kota Palembang 2026",
            "jenis_pks": "PBPU Pemda",
            "npp_ro": 100001,
            "jumlah_anggaran": 2500000000,
            "tmt_nk": "2026-01-01",
            "tat_nk": "2026-12-31",
            "tmt_rk": "2026-01-01",
            "tat_rk": "2026-12-31",
            "status_pks": "Aktif"
        }
    ]
}
```

---

### 3. `GET /api/pks/{kode_pks}` — Ambil Detail PKS

**Response Sukses (HTTP 200):**
```json
{
    "message": "Data PKS berhasil diambil",
    "data": {
        "kode_pks": "16710001",
        "nama_pks": "PKS PBPU Pemda Kota Palembang 2026",
        "jenis_pks": "PBPU Pemda",
        "npp_ro": 100001,
        "jumlah_anggaran": 2500000000,
        "tmt_nk": "2026-01-01",
        "tat_nk": "2026-12-31",
        "tmt_rk": "2026-01-01",
        "tat_rk": "2026-12-31",
        "status_pks": "Aktif",
        "riwayat": [
            {
                "no_riwayat": 1,
                "update_by": 100001,
                "kategori_perubahan": "Data baru ditambahkan",
                "data_lama": null,
                "data_baru": null,
                "created_at": "2026-01-01T00:00:00.000000Z"
            }
        ]
    }
}
```

**Response Not Found (HTTP 404):**
```json
{
    "message": "Data PKS tidak ditemukan"
}
```

---

### 4. `PUT /api/pks/{kode_pks}` — Update Data PKS

> Validasi sama seperti `POST`, namun `kode_pks` tidak wajib unik (ignore dirinya sendiri). Setiap field yang diubah **wajib** mencatat satu baris ke `riwayat_pks`.

**Response Sukses (HTTP 200):**
```json
{
    "message": "Data PKS berhasil diperbarui",
    "data": {
        "kode_pks": "16710001",
        "nama_pks": "PKS PBPU Pemda Kota Palembang 2026 (Revisi)",
        "jenis_pks": "PBPU Pemda",
        "status_pks": "Aktif"
    }
}
```

---

### 5. `DELETE /api/pks/{kode_pks}` — Hapus Data PKS

**Response Sukses (HTTP 200):**
```json
{
    "message": "Data PKS berhasil dihapus"
}
```

---

### 6. `GET /api/pks/{kode_pks}/riwayat` — Ambil Riwayat PKS

**Response Sukses (HTTP 200):**
```json
{
    "message": "Riwayat PKS berhasil diambil",
    "data": [
        {
            "no_riwayat": 1,
            "kode_pks": "16710001",
            "update_by": 100001,
            "kategori_perubahan": "Data baru ditambahkan",
            "data_lama": null,
            "data_baru": null,
            "created_at": "2026-01-01T00:00:00.000000Z"
        }
    ]
}
```

---

## Dummy Data (10 Data PKS)

Seeder harus membuat 10 data PKS berikut. Gunakan NPP dari `StaffSeeder` yang sudah ada (100001–100010) untuk kolom `npp_ro` dan `update_by`.

| # | kode_pks | nama_pks | jenis_pks | npp_ro | jumlah_anggaran | tmt_nk | tat_nk | tmt_rk | tat_rk | status_pks |
|---|---|---|---|---|---|---|---|---|---|---|
| 1 | `16710001` | PKS PBPU Pemda Kota Palembang 2026 | PBPU Pemda | 100001 | 2.500.000.000 | 2026-01-01 | 2026-12-31 | 2026-01-01 | 2026-12-31 | Aktif |
| 2 | `16710002` | PKS PBPU Pemda Kabupaten Banyuasin 2026 | PBPU Pemda | 100002 | 1.800.000.000 | 2026-01-01 | 2026-12-31 | 2026-01-01 | 2026-12-31 | Aktif |
| 3 | `16710003` | PKS PBPU Pemda Kota Prabumulih 2026 | PBPU Pemda | 100003 | 950.000.000 | 2026-03-01 | 2026-12-31 | 2026-03-01 | 2026-12-31 | Aktif |
| 4 | `PKS-KOL-2026-001` | PKS Kolektif PT Pusri Palembang | PBPU Kolektif | 100004 | *(null)* | *(null)* | *(null)* | 2026-05-01 | 2027-04-30 | Aktif |
| 5 | `PKS-KOL-2026-002` | PKS Kolektif PT Pertamina RU III | PBPU Kolektif | 100005 | *(null)* | *(null)* | *(null)* | 2026-06-01 | 2027-05-31 | Aktif |
| 6 | `PKS-KOL-2026-003` | PKS Kolektif PT Bukit Asam Tbk | PBPU Kolektif | 100006 | *(null)* | *(null)* | *(null)* | 2026-04-01 | 2027-03-31 | Aktif |
| 7 | `16710004` | PKS PBPU Pemda Kabupaten OKI 2026 | PBPU Pemda | 100007 | 1.200.000.000 | 2026-02-01 | 2026-12-31 | 2026-02-01 | 2026-12-31 | Aktif |
| 8 | `PKS-KOL-2026-004` | PKS Kolektif PT PLN Sumbagsel | PBPU Kolektif | 100008 | *(null)* | *(null)* | *(null)* | 2026-07-01 | 2027-06-30 | Tidak Aktif |
| 9 | `16710005` | PKS PBPU Pemda Kota Lubuklinggau 2025 | PBPU Pemda | 100009 | 750.000.000 | 2025-01-01 | 2025-12-31 | 2025-01-01 | 2025-12-31 | Tidak Aktif |
| 10 | `PKS-KOL-2025-099` | PKS Kolektif Universitas Sriwijaya | PBPU Kolektif | 100010 | *(null)* | *(null)* | *(null)* | 2025-08-01 | 2026-07-31 | Aktif |

> [!NOTE]
> Seeder PKS **harus dijalankan setelah** `StaffSeeder` karena ada foreign key ke tabel `staffs`. Pastikan urutan di `DatabaseSeeder.php` benar.

---

## Tahapan Implementasi

Ikuti tahapan ini **secara berurutan**. Jangan loncat ke tahap berikutnya sebelum yang sekarang selesai.

---

### Tahap 1: Buat Migration untuk Tabel `pks`

**Perintah:**
```bash
php artisan make:migration create_pks_table --no-interaction
```

**File yang dibuat:** `database/migrations/YYYY_MM_DD_HHMMSS_create_pks_table.php`

**Yang harus dilakukan di dalam file migration:**

```php
// Di dalam method up():
Schema::create('pks', function (Blueprint $table) {
    // kode_pks adalah primary key berupa string (bukan auto-increment)
    $table->string('kode_pks', 50)->primary();

    $table->string('nama_pks');

    $table->enum('jenis_pks', ['PBPU Pemda', 'PBPU Kolektif']);

    // npp_ro nullable karena bisa tidak ada RO yang ditugaskan
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
```

**Verifikasi:** Jalankan `php artisan migrate` dan pastikan tidak ada error.

---

### Tahap 2: Buat Migration untuk Tabel `riwayat_pks`

> [!IMPORTANT]
> Migration ini **harus dibuat setelah** migration tabel `pks` karena ada foreign key ke tabel `pks`.

**Perintah:**
```bash
php artisan make:migration create_riwayat_pks_table --no-interaction
```

**Yang harus dilakukan:**

```php
// Di dalam method up():
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
```

**Verifikasi:** Jalankan `php artisan migrate` dan pastikan tidak ada error.

---

### Tahap 3: Buat Model `Pks`

**Perintah:**
```bash
php artisan make:model Pks --no-interaction
```

**File yang dibuat:** `app/Models/Pks.php`

**Yang harus dilakukan di dalam model:**

```php
<?php

namespace App\Models;

/**
 * Pks Model
 *
 * Tujuan    : Representasi tabel pks di database menggunakan Eloquent ORM.
 *             Model ini digunakan untuk operasi CRUD pada data PKS.
 * Dibuat    : [tanggal hari ini]
 * Perubahan : -
 */

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

// Definisikan kolom yang boleh diisi massal (mass assignment)
#[Fillable([
    'kode_pks', 'nama_pks', 'jenis_pks', 'npp_ro',
    'jumlah_anggaran', 'tmt_nk', 'tat_nk', 'tmt_rk', 'tat_rk', 'status_pks'
])]
class Pks extends Model
{
    // Primary key bukan 'id' dan bukan auto-increment
    protected $primaryKey = 'kode_pks';
    public $incrementing = false;
    protected $keyType = 'string';

    // Tabel pks tidak punya updated_at, hanya timestamps standar
    // Jika tabel tidak punya kolom timestamps (created_at/updated_at), set false
    public $timestamps = false;

    /**
     * Relasi ke model Staff untuk mendapatkan data RO yang bertanggung jawab.
     */
    public function ro(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'npp_ro', 'npp');
    }

    /**
     * Relasi ke model RiwayatPks untuk mendapatkan semua riwayat perubahan PKS ini.
     */
    public function riwayat(): HasMany
    {
        return $this->hasMany(RiwayatPks::class, 'kode_pks', 'kode_pks');
    }
}
```

---

### Tahap 4: Buat Model `RiwayatPks`

**Perintah:**
```bash
php artisan make:model RiwayatPks --no-interaction
```

**File yang dibuat:** `app/Models/RiwayatPks.php`

**Yang harus dilakukan:**

```php
<?php

namespace App\Models;

/**
 * RiwayatPks Model
 *
 * Tujuan    : Representasi tabel riwayat_pks di database.
 *             Model ini digunakan untuk mencatat log setiap perubahan data PKS.
 * Dibuat    : [tanggal hari ini]
 * Perubahan : -
 */

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['kode_pks', 'update_by', 'kategori_perubahan', 'data_lama', 'data_baru'])]
class RiwayatPks extends Model
{
    protected $primaryKey = 'no_riwayat';

    // Tabel ini hanya punya created_at, tidak ada updated_at
    // Kita nonaktifkan timestamps otomatis Laravel karena strukturnya berbeda
    public $timestamps = false;

    /**
     * Relasi ke PKS induk.
     */
    public function pks(): BelongsTo
    {
        return $this->belongsTo(Pks::class, 'kode_pks', 'kode_pks');
    }

    /**
     * Relasi ke staff yang melakukan perubahan.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'update_by', 'npp');
    }
}
```

---

### Tahap 5: Buat Form Request `StorePksRequest`

**Perintah:**
```bash
php artisan make:request StorePksRequest --no-interaction
```

**File yang dibuat:** `app/Http/Requests/StorePksRequest.php`

**Yang harus dilakukan — ini bagian paling kompleks, baca baik-baik:**

```php
<?php

namespace App\Http\Requests;

/**
 * StorePksRequest
 *
 * Tujuan    : Validasi input untuk endpoint POST /api/pks.
 *             Validasi kondisional: jika jenis_pks = 'PBPU Pemda', maka
 *             jumlah_anggaran, tmt_nk, dan tat_nk wajib diisi.
 * Dibuat    : [tanggal hari ini]
 * Perubahan : -
 */

use Illuminate\Foundation\Http\FormRequest;

class StorePksRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Saat ini semua request diizinkan — ubah jika ada autentikasi
        return true;
    }

    public function rules(): array
    {
        return [
            'kode_pks'        => ['required', 'string', 'max:50', 'unique:pks,kode_pks'],
            'nama_pks'        => ['required', 'string', 'max:255'],
            'jenis_pks'       => ['required', 'in:PBPU Pemda,PBPU Kolektif'],
            'npp_ro'          => ['nullable', 'integer', 'exists:staffs,npp'],

            // required_if: wajib diisi HANYA jika jenis_pks = 'PBPU Pemda'
            'jumlah_anggaran' => ['nullable', 'integer', 'min:0', 'required_if:jenis_pks,PBPU Pemda'],
            'tmt_nk'          => ['nullable', 'date_format:Y-m-d', 'required_if:jenis_pks,PBPU Pemda'],
            'tat_nk'          => ['nullable', 'date_format:Y-m-d', 'after_or_equal:tmt_nk', 'required_if:jenis_pks,PBPU Pemda'],

            'tmt_rk'          => ['required', 'date_format:Y-m-d'],
            'tat_rk'          => ['required', 'date_format:Y-m-d', 'after_or_equal:tmt_rk'],
            'status_pks'      => ['nullable', 'in:Aktif,Tidak Aktif'],
        ];
    }

    public function messages(): array
    {
        return [
            'kode_pks.required'          => 'Kolom kode_pks wajib diisi.',
            'kode_pks.unique'            => 'Kode PKS sudah digunakan.',
            'nama_pks.required'          => 'Kolom nama_pks wajib diisi.',
            'jenis_pks.required'         => 'Kolom jenis_pks wajib diisi.',
            'jenis_pks.in'               => 'Kolom jenis_pks harus berisi PBPU Pemda atau PBPU Kolektif.',
            'npp_ro.exists'              => 'NPP RO tidak ditemukan di data staff.',
            'jumlah_anggaran.required_if'=> 'Kolom jumlah_anggaran wajib diisi jika jenis_pks adalah PBPU Pemda.',
            'tmt_nk.required_if'         => 'Kolom tmt_nk wajib diisi jika jenis_pks adalah PBPU Pemda.',
            'tat_nk.required_if'         => 'Kolom tat_nk wajib diisi jika jenis_pks adalah PBPU Pemda.',
            'tat_nk.after_or_equal'      => 'Kolom tat_nk harus sama dengan atau setelah tmt_nk.',
            'tmt_rk.required'            => 'Kolom tmt_rk wajib diisi.',
            'tat_rk.required'            => 'Kolom tat_rk wajib diisi.',
            'tat_rk.after_or_equal'      => 'Kolom tat_rk harus sama dengan atau setelah tmt_rk.',
            'status_pks.in'              => 'Kolom status_pks harus berisi Aktif atau Tidak Aktif.',
        ];
    }
}
```

---

### Tahap 6: Buat Form Request `UpdatePksRequest`

**Perintah:**
```bash
php artisan make:request UpdatePksRequest --no-interaction
```

**File yang dibuat:** `app/Http/Requests/UpdatePksRequest.php`

> Hampir sama seperti `StorePksRequest`, namun rule `kode_pks` menggunakan `unique:pks,kode_pks,{kode_pks},kode_pks` untuk mengabaikan record dirinya sendiri saat update.

---

### Tahap 7: Buat API Controller `PksController`

**Perintah:**
```bash
php artisan make:controller Api/PksController --api --no-interaction
```

**File yang dibuat:** `app/Http/Controllers/Api/PksController.php`

**Struktur method yang harus diimplementasikan:**

```php
<?php

namespace App\Http\Controllers\Api;

/**
 * PksController (API)
 *
 * Tujuan    : Menangani semua operasi CRUD untuk data PKS melalui REST API.
 *             Setiap operasi tulis (store/update/destroy) juga mencatat
 *             perubahan ke tabel riwayat_pks secara otomatis.
 * Dibuat    : [tanggal hari ini]
 * Perubahan : -
 */

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePksRequest;
use App\Http\Requests\UpdatePksRequest;
use App\Models\Pks;
use App\Models\RiwayatPks;
use Illuminate\Http\JsonResponse;

class PksController extends Controller
{
    /**
     * GET /api/pks
     * Mengembalikan semua data PKS, diurutkan dari yang terbaru.
     */
    public function index(): JsonResponse
    {
        // ...
    }

    /**
     * POST /api/pks
     * Menyimpan data PKS baru dan mencatat riwayat penambahan.
     *
     * Penting: Catat riwayat ke tabel riwayat_pks setelah PKS berhasil disimpan.
     * Gunakan DB::transaction() agar jika pencatatan riwayat gagal, data PKS juga ikut dibatalkan (rollback).
     */
    public function store(StorePksRequest $request): JsonResponse
    {
        // Langkah 1: Bungkus dalam DB::transaction()
        // Langkah 2: Buat data PKS dari $request->validated()
        //            Set status_pks default 'Aktif' jika tidak dikirim
        // Langkah 3: Catat ke riwayat_pks:
        //            - kode_pks    : dari data yang baru disimpan
        //            - update_by   : dari npp_ro yang dikirim (atau NPP user yang login di masa depan)
        //            - kategori_perubahan: "Data baru ditambahkan"
        //            - data_lama   : null
        //            - data_baru   : null
        //            - created_at  : now()
        // Langkah 4: Return response 201
    }

    /**
     * GET /api/pks/{kode_pks}
     * Mengembalikan detail PKS beserta riwayat perubahannya.
     */
    public function show(string $kodePks): JsonResponse
    {
        // Load relasi riwayat agar tidak terjadi N+1 query
        // ...
    }

    /**
     * PUT /api/pks/{kode_pks}
     * Memperbarui data PKS dan mencatat setiap field yang berubah ke riwayat_pks.
     */
    public function update(UpdatePksRequest $request, string $kodePks): JsonResponse
    {
        // Langkah 1: Ambil data PKS lama sebelum diupdate (untuk dicatat ke riwayat)
        // Langkah 2: Update data PKS
        // Langkah 3: Untuk setiap field yang berubah, insert satu baris ke riwayat_pks
        //            dengan data_lama = nilai sebelum, data_baru = nilai sesudah
        // ...
    }

    /**
     * DELETE /api/pks/{kode_pks}
     * Menghapus data PKS (riwayat ikut terhapus karena cascadeOnDelete di migration).
     */
    public function destroy(string $kodePks): JsonResponse
    {
        // ...
    }
}
```

---

### Tahap 8: Daftarkan Route API

**File yang harus diedit:** `routes/api.php`

**Tambahkan baris berikut** di bawah route yang sudah ada:

```php
use App\Http\Controllers\Api\PksController;

// Route CRUD untuk data PKS
// Menggunakan apiResource agar semua endpoint (index, store, show, update, destroy) terdaftar otomatis
Route::apiResource('pks', PksController::class);

// Route khusus untuk mengambil riwayat PKS berdasarkan kode_pks
Route::get('pks/{kode_pks}/riwayat', [PksController::class, 'riwayat']);
```

**Verifikasi:** Jalankan `php artisan route:list --path=api/pks` dan pastikan semua route terdaftar.

---

### Tahap 9: Buat Seeder untuk Data Dummy PKS

**Perintah:**
```bash
php artisan make:seeder PksSeeder --no-interaction
```

**File yang dibuat:** `database/seeders/PksSeeder.php`

**Yang harus dilakukan:**

- Masukkan 10 data PKS dari tabel dummy data di atas.
- Setelah setiap insert PKS, **langsung insert juga ke `riwayat_pks`** dengan `kategori_perubahan = "Data baru ditambahkan"`.
- Gunakan NPP dari staff yang sudah ada (100001–100010) untuk `update_by`.

Setelah membuat seeder, **daftarkan di** `database/seeders/DatabaseSeeder.php`:

```php
// Urutan PENTING: StaffSeeder harus sebelum PksSeeder karena ada foreign key
$this->call([
    StaffSeeder::class,
    PksSeeder::class, // Tambahkan ini
]);
```

**Verifikasi:** Jalankan `php artisan db:seed --class=PksSeeder` dan cek data di database.

---

### Tahap 10: Format Kode dengan Pint

**Perintah (wajib dijalankan setelah semua file PHP selesai dibuat/diedit):**
```bash
vendor/bin/pint --dirty --format agent
```

---

### Tahap 11: Tulis Pest Test

**Perintah:**
```bash
php artisan make:test --pest PksApiTest --no-interaction
```

**Test yang harus ditulis di `tests/Feature/PksApiTest.php`:**

| Test Case | Skenario |
|---|---|
| `POST /api/pks` berhasil dengan data PBPU Pemda lengkap | HTTP 201, data tersimpan, riwayat tercatat |
| `POST /api/pks` berhasil dengan data PBPU Kolektif (tanpa anggaran & NK) | HTTP 201 |
| `POST /api/pks` gagal jika PBPU Pemda tapi tmt_nk kosong | HTTP 422, ada pesan error |
| `POST /api/pks` gagal jika kode_pks sudah ada | HTTP 422 |
| `GET /api/pks` mengembalikan semua data | HTTP 200 |
| `GET /api/pks/{kode_pks}` mengembalikan data + riwayat | HTTP 200 |
| `GET /api/pks/{kode_pks}` dengan kode tidak ada | HTTP 404 |
| `PUT /api/pks/{kode_pks}` berhasil update | HTTP 200, riwayat bertambah |
| `DELETE /api/pks/{kode_pks}` berhasil hapus | HTTP 200 |

**Jalankan test:**
```bash
php artisan test --compact --filter=PksApiTest
```

---

## Checklist Akhir

Sebelum dinyatakan selesai, pastikan semua item berikut sudah ✅:

- [ ] Migration `pks` berhasil dijalankan tanpa error
- [ ] Migration `riwayat_pks` berhasil dijalankan tanpa error
- [ ] Model `Pks` dan `RiwayatPks` sudah ada beserta relasi-relasinya
- [ ] `StorePksRequest` dan `UpdatePksRequest` sudah ada dengan validasi kondisional
- [ ] `PksController` sudah mengimplementasikan semua method (index, store, show, update, destroy, riwayat)
- [ ] `POST /api/pks` otomatis mencatat ke `riwayat_pks` setelah berhasil insert
- [ ] Semua route API sudah terdaftar (`php artisan route:list --path=api/pks`)
- [ ] `PksSeeder` berhasil mengisi 10 data PKS + riwayat awalnya
- [ ] `DatabaseSeeder` mendaftarkan `PksSeeder` setelah `StaffSeeder`
- [ ] Pint sudah dijalankan (`vendor/bin/pint --dirty --format agent`)
- [ ] Semua Pest test lulus (`php artisan test --compact --filter=PksApiTest`)

---

> [!TIP]
> Jika ada yang tidak jelas, baca komentar di file-file yang sudah ada sebagai referensi gaya penulisan kode di proyek ini. Semua file wajib memiliki PHPDoc header (Tujuan, Dibuat, Perubahan).

