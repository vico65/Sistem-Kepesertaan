<?php

namespace App\Models;

/**
 * Pks Model
 *
 * Tujuan    : Representasi tabel pks di database menggunakan Eloquent ORM.
 *             Model ini digunakan untuk operasi CRUD pada data PKS.
 * Dibuat    : 2026-11-01
 * Perubahan : -
 */

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'kode_pks',
    'nama_pks',
    'jenis_pks',
    'npp_ro',
    'jumlah_anggaran',
    'tmt_nk',
    'tat_nk',
    'tmt_rk',
    'tat_rk',
    'status_pks',
])]
class Pks extends Model
{
    use HasFactory;

    /**
     * Nama tabel di database.
     */
    protected $table = 'pks';

    /**
     * Primary key bukan 'id' dan bukan auto-increment.
     */
    protected $primaryKey = 'kode_pks';

    public $incrementing = false;

    protected $keyType = 'string';

    /**
     * Tabel pks tidak memiliki timestamps bawaan Laravel (created_at & updated_at).
     */
    public $timestamps = false;

    /**
     * Cast otomatis untuk kolom tertentu.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'jumlah_anggaran' => 'integer',
            'npp_ro' => 'integer',
            'tmt_nk' => 'date:Y-m-d',
            'tat_nk' => 'date:Y-m-d',
            'tmt_rk' => 'date:Y-m-d',
            'tat_rk' => 'date:Y-m-d',
        ];
    }

    /**
     * Relasi ke model Staff untuk data RO yang bertugas.
     */
    public function ro(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'npp_ro', 'npp');
    }

    /**
     * Relasi ke riwayat perubahan PKS.
     */
    public function riwayat(): HasMany
    {
        return $this->hasMany(RiwayatPks::class, 'kode_pks', 'kode_pks');
    }
}
