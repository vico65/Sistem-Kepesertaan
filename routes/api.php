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
