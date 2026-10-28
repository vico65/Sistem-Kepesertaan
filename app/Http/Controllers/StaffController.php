<?php

namespace App\Http\Controllers;

/**
 * StaffController
 *
 * Tujuan    : Menangani tampilan dan pengelolaan data staff pegawai.
 * Dibuat    : 2026-10-27
 * Perubahan : Menambahkan method index untuk menampilkan daftar staff
 */

use App\Models\Staff;
use Illuminate\Contracts\View\View;

class StaffController extends Controller
{
    /**
     * Menampilkan halaman daftar pegawai (list staff).
     */
    public function index(): View
    {
        // Ambil semua data staff, urutkan berdasarkan nama
        $staffs = Staff::orderBy('nama')->get();

        return view('staff.index', compact('staffs'));
    }
}
