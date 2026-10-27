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
use Illuminate\Support\Facades\Validator;

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
        // Validasi input dari request body
        $validator = Validator::make($request->all(), [
            'npp' => ['required', 'integer'],
            'password' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => 'Login gagal',
            ], 400);
        }

        // Cari staff berdasarkan NPP yang dikirim
        $staff = Staff::find($request->npp);

        // Cek: apakah staff ditemukan DAN password yang dikirim cocok dengan yang tersimpan
        if (! $staff || ! Hash::check($request->password, $staff->password)) {
            // Jika tidak cocok, kembalikan response error 400
            return response()->json([
                'status' => 400,
                'message' => 'Login gagal',
            ], 400);
        }

        // Cek: apakah status staff masih aktif
        if ($staff->status !== 'aktif') {
            return response()->json([
                'status' => 400,
                'message' => 'Login gagal',
            ], 400);
        }

        // Jika semua validasi lulus, kembalikan response sukses 200
        return response()->json([
            'status' => 200,
            'message' => 'Login berhasil',
        ], 200);
    }
}
