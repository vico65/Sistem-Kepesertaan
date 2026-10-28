# 📋 ISSUE — Halaman List Pegawai (Tampilan Frontend)

> **Target Implementor:** Junior Programmer / AI Model
> **Dibuat oleh:** Lead Programmer
> **Tanggal:** 2026-10-27
> **Branch yang harus dibuat:** `feature/list-pegawai`

---

## 🎯 Tujuan

Membangun halaman **"List Pegawai"** yang menampilkan seluruh data staff dari tabel `staffs`. Pada tahap ini, implementasi **hanya di sisi tampilan (frontend)** — backend CRUD (tambah/update ke database) di-hold ke branch berikutnya.

---

## 🏗️ Arsitektur yang Harus Diikuti

### Pemecahan `dashboard.blade.php` menjadi Blade Layout

Saat ini, `dashboard.blade.php` memiliki header dan sidebar yang ditulis langsung di satu file. Agar bisa digunakan ulang di halaman lain, **pecah dulu** menjadi komponen layout standar Laravel:

```
resources/views/
├── layouts/
│   └── app.blade.php        ← Layout utama (header + sidebar + @yield('content'))
├── dashboard.blade.php      ← Di-refactor: extends layout
└── staff/
    └── index.blade.php      ← Halaman baru: List Pegawai
```

> ⚠️ **PENTING:** Jangan ubah konten/tampilan header dan sidebar. Hanya pindahkan strukturnya ke `layouts/app.blade.php`.

---

## 📦 Tahapan Implementasi

Ikuti secara berurutan.

---

### ✅ Tahap 1 — Buat Layout Utama: `resources/views/layouts/app.blade.php`

Buat file baru ini dengan memindahkan seluruh struktur `<html>`, `<head>`, `<header>`, dan `<nav>` dari `dashboard.blade.php`.

Struktur utama file ini:

```php
<!DOCTYPE html>
<html lang="id">
<head>
    <title>@yield('title', 'Sistem Kepesertaan') - Kepesertaan KC Palembang</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body ...>
    <div ...>
        {{-- HEADER (salin dari dashboard.blade.php) --}}
        <header ...> ... </header>

        {{-- SIDEBAR (salin dari dashboard.blade.php) --}}
        <nav id="sidebar" ...>
            {{--
                PERUBAHAN PENTING: Tambahkan active state pada tiap menu menggunakan:
                class="{{ request()->routeIs('dashboard') ? 'menu-selected' : '' }}"
                Ganti 'dashboard' dengan nama route yang sesuai tiap menu.
            --}}
            ...
        </nav>

        {{-- KONTEN DINAMIS --}}
        <main class="w-full pt-14 sm:pl-52 min-h-screen">
            <section class="p-4 sm:p-6">
                @yield('content')
            </section>
        </main>
    </div>

    @stack('scripts') {{-- Penting! Untuk @push('scripts') di tiap view --}}
</body>
</html>
```

**Poin penting pada sidebar:**
- Setiap `<a>` menu harus menggunakan `href="{{ route('nama.route') }}"`
- Tambahkan kondisi active: `class="menu-list {{ request()->routeIs('dashboard') ? 'menu-selected' : '' }}"`
- Named routes yang diperlukan: `dashboard`, `staff.index`

---

### ✅ Tahap 2 — Refactor `dashboard.blade.php`

Setelah layout dibuat, ubah seluruh isi `dashboard.blade.php` menjadi:

```php
{{--
    Diubah: 2026-10-27
    Perubahan: Di-refactor agar menggunakan layouts/app.blade.php
--}}
@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <h2 class="font-bold font-poppins text-xl text-slate-800">Dashboard</h2>
    <p class="font-inter text-sm text-slate-500 mt-1">Selamat datang di Sistem Kepesertaan.</p>
@endsection
```

---

### ✅ Tahap 3 — Update `routes/web.php`

```php
{{--
    Diubah: 2026-10-27
    Perubahan: Menambahkan named routes dan route untuk List Pegawai
--}}

use App\Http\Controllers\StaffController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('dashboard');
})->name('dashboard');

Route::get('/pegawai', [StaffController::class, 'index'])->name('staff.index');
```

---

### ✅ Tahap 4 — Buat `StaffController`

```bash
php artisan make:controller StaffController --no-interaction
```

Isi method `index()`:

```php
public function index(Request )
{
    // Ambil semua data staff, urutkan berdasarkan nama
     = Staff::orderBy('nama')->get();

    return view('staff.index', compact('staffs'));
}
```

---

### ✅ Tahap 5 — Buat `resources/views/staff/index.blade.php`

Gunakan `@extends('layouts.app')` dan `@section('content')`.

**Komponen wajib dalam tampilan (ikuti UI-Guideline.md):**

**A. Header Halaman:**
- Judul: `"Daftar Staf"` — gunakan `font-poppins font-bold text-xl`
- Subjudul: `"Manajemen data pegawai dan penanggung jawab PKS"` — gunakan `font-inter text-sm text-slate-500`
- Tombol `+ Tambah Staf` di kanan atas — warna `bg-primary-biru` (warna utama sistem)

**B. Bar Toolbar:**
- Input Search (`id="input_search"`) dengan ikon kaca pembesar (inline SVG dari Myna UI)
- Dropdown Filter Status (`id="filter_status"`): opsi Semua, Aktif, Nonaktif/Mutasi, Keluar

**C. Tabel Data Staff:**
- Kolom: NPP, Nama Staf, No. WhatsApp, Email, Status, Aksi
- Tiap `<tr>` harus memiliki atribut: `data-nama`, `data-npp`, `data-status` (untuk filter JS)
- Badge Status:
  - Aktif → hijau (`bg-primary-hijau/15 text-primary-hijau`)
  - Nonaktif/Mutasi → abu-abu (`bg-slate-200 text-slate-500`)
  - Keluar → merah (`bg-red-100 text-red-500`)
- Kolom Aksi:
  - Tombol Edit (ikon pensil, buka modal edit dengan `onclick="bukaModalEdit(...)"` )
  - Tombol Detail (ikon mata, `href="#"` untuk saat ini)

**D. Modal Tambah Staf (Pop Up):**
- `id="modal_tambah_staf"` — default `hidden`
- Field: NPP, Nama, Email, No. WhatsApp, Status (dropdown)
- Tombol Batal dan Simpan
- `action="#"` — backend di-hold ke branch berikutnya

**E. Modal Edit Staf (Pop Up):**
- `id="modal_edit_staf"` — default `hidden`
- Field sama dengan modal tambah, tapi NPP bersifat `readonly`
- Field diisi otomatis via JavaScript saat tombol Edit diklik
- `action="#"` — backend di-hold ke branch berikutnya

**F. JavaScript (gunakan `@push('scripts')`):**
- Fungsi `filterTabel()`: filter baris tabel berdasarkan keyword dan status secara instan
- Fungsi `bukaModalEdit(npp, nama, nomorHp, email, status)`: mengisi form modal edit
- Event listener: tutup modal saat klik di luar area modal (backdrop)

---

### ✅ Tahap 6 — Jalankan Pint

```bash
vendor/bin/pint --dirty --format agent
```

---

## 📁 Ringkasan File yang Harus Dibuat/Diubah

| No | File | Aksi |
|----|------|------|
| 1 | `resources/views/layouts/app.blade.php` | **Buat baru** |
| 2 | `resources/views/dashboard.blade.php` | **Ubah** — refactor extends layout |
| 3 | `resources/views/staff/index.blade.php` | **Buat baru** |
| 4 | `app/Http/Controllers/StaffController.php` | **Buat baru** |
| 5 | `routes/web.php` | **Ubah** — tambahkan named routes |

---

## 🚫 Yang TIDAK Boleh Dilakukan

- ❌ Jangan ubah tampilan visual header dan sidebar
- ❌ Jangan implementasikan backend (POST/PUT ke database) di branch ini
- ❌ Jangan install package/dependency baru tanpa izin Lead Programmer
- ❌ Jangan gunakan font atau warna di luar yang sudah didefinisikan di `app.css`

---

## ✔️ Definition of Done (Checklist)

- [ ] `layouts/app.blade.php` sudah ada (header + sidebar)
- [ ] `dashboard.blade.php` di-refactor, tampilan tidak berubah
- [ ] Halaman `/pegawai` menampilkan tabel data dari database
- [ ] Pencarian dan filter status berfungsi secara instan (JS)
- [ ] Tombol `+ Tambah Staf` membuka popup
- [ ] Tombol Edit di tiap baris membuka popup dengan data yang sudah terisi
- [ ] Badge status menampilkan warna yang benar
- [ ] Menu aktif di sidebar menyesuaikan halaman yang sedang dibuka
- [ ] Pint sudah dijalankan
- [ ] Tidak ada error di `php artisan route:list`
