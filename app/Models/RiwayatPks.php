<?php

namespace App\Models;

/**
 * RiwayatPks Model
 *
 * Tujuan    : Representasi tabel riwayat_pks di database.
 *             Digunakan untuk audit trail perubahan data PKS.
 * Dibuat    : 2026-11-01
 * Perubahan : -
 */

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'kode_pks',
    'update_by',
    'kategori_perubahan',
    'data_lama',
    'data_baru',
])]
class RiwayatPks extends Model
{
    use HasFactory;

    /**
     * Nama tabel di database.
     */
    protected $table = 'riwayat_pks';

    /**
     * Primary key.
     */
    protected $primaryKey = 'no_riwayat';

    /**
     * Tabel ini hanya memiliki kolom created_at (tanpa updated_at),
     * timestamps default Laravel dinonaktifkan agar tidak mencari updated_at.
     */
    public $timestamps = false;

    /**
     * Cast otomatis untuk kolom.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'update_by' => 'integer',
            'created_at' => 'datetime',
        ];
    }

    /**
     * Relasi ke PKS induk.
     */
    public function pks(): BelongsTo
    {
        return $this->belongsTo(Pks::class, 'kode_pks', 'kode_pks');
    }

    /**
     * Relasi ke staff yang melakukan perubahan.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'update_by', 'npp');
    }
}
