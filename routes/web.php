<?php

/**
 * routes/web.php
 *
 * Diubah: 2026-10-27
 * Perubahan: Menambahkan named routes dan route untuk List Pegawai
 */

use App\Http\Controllers\StaffController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('dashboard');
})->name('dashboard');

Route::get('/pegawai', [StaffController::class, 'index'])->name('staff.index');
