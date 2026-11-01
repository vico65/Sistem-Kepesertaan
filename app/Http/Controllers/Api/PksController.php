<?php

namespace App\Http\Controllers\Api;

/**
 * PksController (API)
 *
 * Tujuan    : Menangani semua operasi CRUD untuk data PKS melalui REST API.
 *             Setiap operasi tulis (store/update) juga mencatat
 *             perubahan ke tabel riwayat_pks secara otomatis.
 * Dibuat    : 2026-11-01
 * Perubahan : -
 */

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePksRequest;
use App\Http\Requests\UpdatePksRequest;
use App\Models\Pks;
use App\Models\RiwayatPks;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class PksController extends Controller
{
    /**
     * GET /api/pks
     * Mengambil daftar seluruh dokumen PKS.
     */
    public function index(): JsonResponse
    {
        $pksList = Pks::all();

        return response()->json([
            'message' => 'Data PKS berhasil diambil',
            'data' => $pksList,
        ], Response::HTTP_OK);
    }

    /**
     * POST /api/pks
     * Menyimpan data PKS baru dan otomatis mencatat riwayat penambahan awal.
     */
    public function store(StorePksRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            // Set default status_pks jika tidak diberikan
            if (! isset($validated['status_pks'])) {
                $validated['status_pks'] = 'Aktif';
            }

            $pks = DB::transaction(function () use ($validated) {
                // Simpan PKS baru
                $newPks = Pks::create($validated);

                // Tentukan siapa yang melakukan penambahan (update_by)
                // Utamakan npp_ro yang dikirim, jika kosong ambil staff aktif pertama sebagai fallback
                $updaterNpp = $validated['npp_ro']
                    ?? auth()->user()?->npp
                    ?? DB::table('staffs')->value('npp');

                // Catat ke riwayat_pks
                RiwayatPks::create([
                    'kode_pks' => $newPks->kode_pks,
                    'update_by' => $updaterNpp,
                    'kategori_perubahan' => 'Data baru ditambahkan',
                    'data_lama' => null,
                    'data_baru' => null,
                ]);

                return $newPks;
            });

            return response()->json([
                'message' => 'Data PKS berhasil ditambahkan',
                'data' => [
                    'kode_pks' => $pks->kode_pks,
                    'nama_pks' => $pks->nama_pks,
                    'jenis_pks' => $pks->jenis_pks,
                    'status_pks' => $pks->status_pks,
                ],
            ], Response::HTTP_CREATED);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Data PKS gagal ditambahkan',
                'errors' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * GET /api/pks/{kode_pks}
     * Mengambil detail dokumen PKS beserta riwayat perubahannya.
     */
    public function show(string $kodePks): JsonResponse
    {
        $pks = Pks::with('riwayat')->find($kodePks);

        if (! $pks) {
            return response()->json([
                'message' => 'Data PKS tidak ditemukan',
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'message' => 'Data PKS berhasil diambil',
            'data' => $pks,
        ], Response::HTTP_OK);
    }

    /**
     * PUT /api/pks/{kode_pks}
     * Memperbarui data PKS dan mencatat setiap perubahan field ke riwayat_pks.
     */
    public function update(UpdatePksRequest $request, string $kodePks): JsonResponse
    {
        $pks = Pks::find($kodePks);

        if (! $pks) {
            return response()->json([
                'message' => 'Data PKS tidak ditemukan',
            ], Response::HTTP_NOT_FOUND);
        }

        try {
            $validated = $request->validated();

            DB::transaction(function () use ($pks, $validated) {
                $updaterNpp = $validated['update_by']
                    ?? $validated['npp_ro']
                    ?? $pks->npp_ro
                    ?? auth()->user()?->npp
                    ?? DB::table('staffs')->value('npp');

                // Hilangkan update_by dari data update model Pks
                unset($validated['update_by']);

                // Periksa perubahan nilai per field untuk dicatat ke riwayat_pks
                foreach ($validated as $field => $newValue) {
                    $oldValue = $pks->{$field};

                    // Normalisasi untuk perbandingan string jika diperlukan
                    $oldStr = $oldValue instanceof \DateTimeInterface ? $oldValue->format('Y-m-d') : (string) $oldValue;
                    $newStr = (string) $newValue;

                    if ($oldStr !== $newStr) {
                        RiwayatPks::create([
                            'kode_pks' => $pks->kode_pks,
                            'update_by' => $updaterNpp,
                            'kategori_perubahan' => "Ubah {$field}",
                            'data_lama' => substr((string) ($oldValue ?? ''), 0, 99),
                            'data_baru' => substr((string) ($newValue ?? ''), 0, 99),
                        ]);
                    }
                }

                $pks->update($validated);
            });

            return response()->json([
                'message' => 'Data PKS berhasil diperbarui',
                'data' => [
                    'kode_pks' => $pks->kode_pks,
                    'nama_pks' => $pks->nama_pks,
                    'jenis_pks' => $pks->jenis_pks,
                    'status_pks' => $pks->status_pks,
                ],
            ], Response::HTTP_OK);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Data PKS gagal diperbarui',
                'errors' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * DELETE /api/pks/{kode_pks}
     * Menghapus data PKS (riwayat ikut terhapus via cascade on delete).
     */
    public function destroy(string $kodePks): JsonResponse
    {
        $pks = Pks::find($kodePks);

        if (! $pks) {
            return response()->json([
                'message' => 'Data PKS tidak ditemukan',
            ], Response::HTTP_NOT_FOUND);
        }

        $pks->delete();

        return response()->json([
            'message' => 'Data PKS berhasil dihapus',
        ], Response::HTTP_OK);
    }

    /**
     * GET /api/pks/{kode_pks}/riwayat
     * Mengambil riwayat perubahan spesifik satu kode PKS.
     */
    public function riwayat(string $kodePks): JsonResponse
    {
        $pks = Pks::find($kodePks);

        if (! $pks) {
            return response()->json([
                'message' => 'Data PKS tidak ditemukan',
            ], Response::HTTP_NOT_FOUND);
        }

        $riwayat = RiwayatPks::where('kode_pks', $kodePks)->get();

        return response()->json([
            'message' => 'Riwayat PKS berhasil diambil',
            'data' => $riwayat,
        ], Response::HTTP_OK);
    }
}
