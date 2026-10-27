<?php

namespace App\Models;

/**
 * Staff Model
 *
 * Tujuan    : Representasi tabel staffs di database menggunakan Eloquent ORM.
 *             Model ini digunakan untuk operasi CRUD pada data staff.
 * Dibuat    : 2026-10-27
 * Perubahan : -
 */

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// Staff extends Authenticatable agar bisa digunakan dengan sistem auth Laravel
#[Fillable(['npp', 'nama', 'email', 'nomor_hp', 'password', 'status'])]
class Staff extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Nama tabel di database.
     * Secara default plural dari 'Staff' adalah 'staff', maka harus didefinisikan eksplisit 'staffs'.
     */
    protected $table = 'staffs';

    /**
     * Primary key menggunakan 'npp' (bukan default 'id').
     * Tidak auto-increment karena NPP ditentukan manual.
     */
    protected $primaryKey = 'npp';

    public $incrementing = false;

    protected $keyType = 'integer';

    /**
     * Kolom yang disembunyikan saat model di-serialize ke JSON.
     * Password tidak boleh pernah dikembalikan ke response API.
     */
    protected $hidden = ['password'];

    /**
     * Cast otomatis untuk tipe data kolom tertentu.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed', // Password otomatis di-hash oleh Laravel saat diset
        ];
    }
}
