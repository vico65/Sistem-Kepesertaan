# 📋 ISSUE — Fitur Autentikasi Staff (Login API)

> **Target Implementor:** Junior Programmer / AI Model
> **Dibuat oleh:** Lead Programmer
> **Tanggal:** 2026-10-27
> **Branch yang harus dibuat:** `feature/auth-staff`

---

## 🎯 Tujuan

Mengimplementasikan tabel `staffs`, API login untuk staff, seeder dummy data, dan memastikan setiap perubahan kode diberi komentar yang jelas agar Lead Programmer dapat memahami perubahan tersebut.

---

## 📐 Konvensi Komentar Kode

> ⚠️ **WAJIB** — Setiap file yang kamu buat atau ubah **harus** memiliki komentar di bagian atas dan di area yang relevan.

**Format komentar untuk setiap file:**

```php
/**
 * [NamaFile].php
 *
 * Tujuan    : [Jelaskan apa fungsi file ini]
 * Dibuat    : [Tanggal]
 * Diubah    : [Tanggal jika ada perubahan]
 * Perubahan : [Jelaskan apa yang diubah]
 */
```

**Untuk setiap method/fungsi, tambahkan komentar singkat:**

```php
/**
 * Login staff menggunakan NPP dan password.
 * Mengembalikan response JSON sesuai format yang ditentukan.
 */
public function login(Request $request): JsonResponse
{
    // Validasi input dari request body
    // Cek apakah staff dengan NPP tersebut ada di database
    // Verifikasi password menggunakan Hash::check()
    // Kembalikan response JSON sesuai format yang ditentukan
}
```

---

## 📦 Tahapan Implementasi

Ikuti tahapan ini **secara berurutan**. Jangan lompat ke tahapan berikutnya sebelum tahapan sebelumnya selesai.

---

### ✅ Tahap 1 — Buat Migration Tabel `staffs`

**File yang dibuat:** `database/migrations/xxxx_xx_xx_xxxxxx_create_staffs_table.php`

Jalankan perintah berikut di terminal:

```bash
php artisan make:migration create_staffs_table --no-interaction
```

Kemudian buka file migration yang baru dibuat dan isi dengan struktur berikut:

```php
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
```

Setelah file migration ditulis, jalankan:

```bash
php artisan migrate --no-interaction
```

---

### ✅ Tahap 2 — Buat Model `Staff`

**File yang dibuat:** `app/Models/Staff.php`

```bash
php artisan make:model Staff --no-interaction
```

Isi model dengan kode berikut:

```php
<?php

namespace App\Models;

/**
 * Staff Model
 *
 * Tujuan    : Representasi tabel staffs di database menggunakan Eloquent ORM.
 *             Model ini digunakan untuk operasi CRUD pada data staff.
 * Dibuat    : 2026-10-27
 * Perubahan : -
 */

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// Staff extends Authenticatable agar bisa digunakan dengan sistem auth Laravel
#[Fillable(['npp', 'nama', 'email', 'nomor_hp', 'password', 'status'])]
class Staff extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Primary key menggunakan 'npp' (bukan default 'id').
     * Tidak auto-increment karena NPP ditentukan manual.
     */
    protected $primaryKey = 'npp';
    public $incrementing = false;
    protected $keyType = 'integer';

    /**
     * Kolom yang disembunyikan saat model di-serialize ke JSON.
     * Password tidak boleh pernah dikembalikan ke response API.
     */
    protected $hidden = ['password'];

    /**
     * Cast otomatis untuk tipe data kolom tertentu.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed', // Password otomatis di-hash oleh Laravel saat diset
        ];
    }
}
```

---

### ✅ Tahap 3 — Buat Factory `StaffFactory`

**File yang dibuat:** `database/factories/StaffFactory.php`

```bash
php artisan make:factory StaffFactory --model=Staff --no-interaction
```

Isi factory dengan kode berikut:

```php
<?php

namespace Database\Factories;

/**
 * StaffFactory
 *
 * Tujuan    : Membuat data dummy untuk tabel staffs menggunakan Faker.
 *             Digunakan oleh seeder dan testing.
 * Dibuat    : 2026-10-27
 * Perubahan : -
 */

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class StaffFactory extends Factory
{
    /**
     * Definisi data default yang dibuat oleh factory ini.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // NPP 6 digit acak antara 100000 - 999999
            'npp'      => $this->faker->unique()->numberBetween(100000, 999999),
            'nama'     => $this->faker->name(),
            'email'    => $this->faker->unique()->safeEmail(),
            'nomor_hp' => '08' . $this->faker->numerify('#########'),
            // Password default untuk semua dummy data adalah 'password123'
            'password' => Hash::make('password123'),
            'status'   => $this->faker->randomElement(['aktif', 'nonaktif', 'keluar']),
        ];
    }
}
```

---

### ✅ Tahap 4 — Buat Seeder dengan 10 Data Dummy

**File yang dibuat:** `database/seeders/StaffSeeder.php`

```bash
php artisan make:seeder StaffSeeder --no-interaction
```

Isi seeder dengan 10 data dummy yang spesifik:

```php
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
            ['npp' => 100010, 'nama' => 'Indah Permatasari',   'email' => 'indah.permatasari@kepesertaan.id','nomor_hp' => '081123456789', 'status' => 'aktif'],
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
```

Daftarkan `StaffSeeder` di `database/seeders/DatabaseSeeder.php`:

```php
// Di dalam method run() pada DatabaseSeeder.php, tambahkan:
$this->call([
    StaffSeeder::class,
]);
```

Jalankan seeder:

```bash
php artisan db:seed --class=StaffSeeder --no-interaction
```

---

### ✅ Tahap 5 — Buat Controller `AuthStaffController`

**File yang dibuat:** `app/Http/Controllers/Api/AuthStaffController.php`

```bash
php artisan make:controller Api/AuthStaffController --no-interaction
```

Isi controller dengan kode berikut:

```php
<?php

namespace App\Http\Controllers\Api;

/**
 * AuthStaffController
 *
 * Tujuan    : Menangani proses autentikasi (login) untuk Staff.
 *             Endpoint ini diakses melalui POST /api/users/login.
 * Dibuat    : 2026-10-27
 * Perubahan : -
 */

use App\Http\Controllers\Controller;
use App\Models\Staff;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthStaffController extends Controller
{
    /**
     * Login staff menggunakan NPP dan password.
     *
     * Endpoint : POST /api/users/login
     *
     * Request Body:
     * {
     *   "npp": "100001",
     *   "password": "password123"
     * }
     *
     * Response (200 - Berhasil):
     * { "status": 200, "message": "Login berhasil" }
     *
     * Response (400 - Gagal):
     * { "status": 400, "message": "Login gagal" }
     */
    public function login(Request $request): JsonResponse
    {
        // Validasi bahwa 'npp' dan 'password' wajib ada di request
        $request->validate([
            'npp'      => ['required', 'integer'],
            'password' => ['required', 'string'],
        ]);

        // Cari staff berdasarkan NPP yang dikirim
        $staff = Staff::find($request->npp);

        // Cek: apakah staff ditemukan DAN password yang dikirim cocok dengan yang tersimpan
        if (! $staff || ! Hash::check($request->password, $staff->password)) {
            // Jika tidak cocok, kembalikan response error 400
            return response()->json([
                'status'  => 400,
                'message' => 'Login gagal',
            ], 400);
        }

        // Cek: apakah status staff masih aktif
        if ($staff->status !== 'aktif') {
            return response()->json([
                'status'  => 400,
                'message' => 'Login gagal',
            ], 400);
        }

        // Jika semua validasi lulus, kembalikan response sukses 200
        return response()->json([
            'status'  => 200,
            'message' => 'Login berhasil',
        ], 200);
    }
}
```

---

### ✅ Tahap 6 — Daftarkan Route API

**File yang diubah:** `routes/api.php`

> ⚠️ Jika file `routes/api.php` **belum ada**, jalankan dulu:
> ```bash
> php artisan install:api --no-interaction
> ```

Tambahkan route berikut ke `routes/api.php`:

```php
<?php

/**
 * routes/api.php
 *
 * Tujuan    : Mendaftarkan semua route untuk API publik sistem ini.
 * Diubah    : 2026-10-27
 * Perubahan : Menambahkan route POST /users/login untuk autentikasi staff
 */

use App\Http\Controllers\Api\AuthStaffController;
use Illuminate\Support\Facades\Route;

// Route login staff — tidak memerlukan autentikasi (publik)
Route::post('/users/login', [AuthStaffController::class, 'login']);
```

Verifikasi route sudah terdaftar:

```bash
php artisan route:list --path=api
```

---

### ✅ Tahap 7 — Testing Manual

Gunakan **Postman**, **Insomnia**, atau **curl** untuk menguji endpoint.

**Test Case 1 — Login Berhasil:**
```
POST http://localhost/sistem-kepesertaan/public/api/users/login
Content-Type: application/json

Body:
{
    "npp": "100001",
    "password": "password123"
}

Expected Response (200):
{
    "status": 200,
    "message": "Login berhasil"
}
```

**Test Case 2 — Login Gagal (password salah):**
```
POST http://localhost/sistem-kepesertaan/public/api/users/login
Content-Type: application/json

Body:
{
    "npp": "100001",
    "password": "salah123"
}

Expected Response (400):
{
    "status": 400,
    "message": "Login gagal"
}
```

**Test Case 3 — Login Gagal (NPP tidak ada):**
```
Body: { "npp": "999999", "password": "password123" }
Expected Response: status 400, message "Login gagal"
```

**Test Case 4 — Login Gagal (status nonaktif/keluar):**
```
Gunakan npp: 100006 (nonaktif) atau npp: 100009 (keluar)
Expected Response: status 400, message "Login gagal"
```

---

### ✅ Tahap 8 — Jalankan Pint (Code Formatter)

Setelah semua kode selesai ditulis, **wajib** jalankan formatter:

```bash
vendor/bin/pint --dirty --format agent
```

---

## 📁 Ringkasan File yang Harus Dibuat/Diubah

| No | File | Aksi |
|----|------|------|
| 1 | `database/migrations/xxxx_create_staffs_table.php` | **Buat baru** |
| 2 | `app/Models/Staff.php` | **Buat baru** |
| 3 | `database/factories/StaffFactory.php` | **Buat baru** |
| 4 | `database/seeders/StaffSeeder.php` | **Buat baru** |
| 5 | `database/seeders/DatabaseSeeder.php` | **Ubah** — daftarkan StaffSeeder |
| 6 | `app/Http/Controllers/Api/AuthStaffController.php` | **Buat baru** |
| 7 | `routes/api.php` | **Buat/Ubah** |

---

## 🚫 Yang TIDAK Boleh Dilakukan

- ❌ Jangan simpan password dalam bentuk plain text — selalu gunakan `Hash::make()`
- ❌ Jangan ubah file migration yang sudah ada (`create_users_table`, dll)
- ❌ Jangan hapus atau ubah model `User.php` yang sudah ada
- ❌ Jangan buat folder baru di luar struktur yang sudah ada
- ❌ Jangan install package/dependency baru tanpa izin Lead Programmer

---

## ✔️ Definition of Done (Checklist Selesai)

Implementasi dianggap selesai jika semua item berikut terpenuhi:

- [ ] Migration `staffs` berhasil dijalankan (`php artisan migrate`)
- [ ] Model `Staff` ada di `app/Models/Staff.php`
- [ ] 10 data dummy berhasil dimasukkan ke tabel `staffs`
- [ ] Endpoint `POST /api/users/login` mengembalikan `200` untuk login valid
- [ ] Endpoint `POST /api/users/login` mengembalikan `400` untuk login tidak valid
- [ ] Staff dengan status `nonaktif` atau `keluar` tidak bisa login
- [ ] Semua file memiliki komentar sesuai konvensi yang ditentukan
- [ ] `vendor/bin/pint --dirty --format agent` sudah dijalankan
- [ ] Tidak ada error saat menjalankan `php artisan route:list --path=api`
