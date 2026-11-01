<?php

/**
 * routes/api.php
 *
 * Tujuan    : Mendaftarkan semua route untuk API publik sistem ini.
 * Diubah    : 2026-11-01
 * Perubahan : Menambahkan route CRUD PKS dan riwayat PKS
 */

use App\Http\Controllers\Api\AuthStaffController;
use App\Http\Controllers\Api\PksController;
use Illuminate\Support\Facades\Route;

// Route login staff — tidak memerlukan autentikasi (publik)
Route::post('/users/login', [AuthStaffController::class, 'login']);

// Route CRUD untuk data PKS
Route::apiResource('pks', PksController::class);

// Route khusus untuk mengambil riwayat PKS berdasarkan kode_pks
Route::get('pks/{kode_pks}/riwayat', [PksController::class, 'riwayat']);
