<?php

namespace App\Http\Requests;

/**
 * StorePksRequest
 *
 * Tujuan    : Validasi input untuk endpoint POST /api/pks.
 *             Menjalankan validasi kondisional: jika jenis_pks = 'PBPU Pemda', maka
 *             jumlah_anggaran, tmt_nk, dan tat_nk wajib diisi.
 * Dibuat    : 2026-11-01
 * Perubahan : -
 */

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

class StorePksRequest extends FormRequest
{
    /**
     * Tentukan apakah user berhak melakukan request ini.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Aturan validasi untuk request penambahan PKS baru.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'kode_pks' => ['required', 'string', 'max:50', 'unique:pks,kode_pks'],
            'nama_pks' => ['required', 'string', 'max:255'],
            'jenis_pks' => ['required', 'in:PBPU Pemda,PBPU Kolektif'],
            'npp_ro' => ['nullable', 'integer', 'exists:staffs,npp'],

            // Wajib jika jenis_pks = 'PBPU Pemda'
            'jumlah_anggaran' => ['nullable', 'integer', 'min:0', 'required_if:jenis_pks,PBPU Pemda'],
            'tmt_nk' => ['nullable', 'date_format:Y-m-d', 'required_if:jenis_pks,PBPU Pemda'],
            'tat_nk' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:tmt_nk', 'required_if:jenis_pks,PBPU Pemda'],

            'tmt_rk' => ['required', 'date_format:Y-m-d'],
            'tat_rk' => ['required', 'date_format:Y-m-d', 'after_or_equal:tmt_rk'],
            'status_pks' => ['nullable', 'in:Aktif,Tidak Aktif'],
        ];
    }

    /**
     * Pesan kustom untuk error validasi.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'kode_pks.required' => 'Kolom kode_pks wajib diisi.',
            'kode_pks.unique' => 'Kode PKS sudah digunakan.',
            'nama_pks.required' => 'Kolom nama_pks wajib diisi.',
            'jenis_pks.required' => 'Kolom jenis_pks wajib diisi.',
            'jenis_pks.in' => 'Kolom jenis_pks harus berisi PBPU Pemda atau PBPU Kolektif.',
            'npp_ro.exists' => 'NPP RO tidak ditemukan di data staff.',
            'jumlah_anggaran.required_if' => 'Kolom jumlah_anggaran wajib diisi jika jenis_pks adalah PBPU Pemda.',
            'tmt_nk.required_if' => 'Kolom tmt_nk wajib diisi jika jenis_pks adalah PBPU Pemda.',
            'tat_nk.required_if' => 'Kolom tat_nk wajib diisi jika jenis_pks adalah PBPU Pemda.',
            'tat_nk.after_or_equal' => 'Kolom tat_nk harus sama dengan atau setelah tmt_nk.',
            'tmt_rk.required' => 'Kolom tmt_rk wajib diisi.',
            'tat_rk.required' => 'Kolom tat_rk wajib diisi.',
            'tat_rk.after_or_equal' => 'Kolom tat_rk harus sama dengan atau setelah tmt_rk.',
            'status_pks.in' => 'Kolom status_pks harus berisi Aktif atau Tidak Aktif.',
        ];
    }

    /**
     * Format response JSON saat validasi gagal sesuai spesifikasi issue:
     * {
     *   "message": "Data PKS gagal ditambahkan",
     *   "errors": { ... }
     * }
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Data PKS gagal ditambahkan',
            'errors' => $validator->errors(),
        ], Response::HTTP_UNPROCESSABLE_ENTITY));
    }
}
