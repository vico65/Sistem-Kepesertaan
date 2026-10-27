<?php

namespace Database\Factories;

/**
 * StaffFactory
 *
 * Tujuan    : Membuat data dummy untuk tabel staffs menggunakan Faker.
 *             Digunakan oleh seeder dan testing.
 * Dibuat    : 2026-10-27
 * Perubahan : -
 */

use App\Models\Staff;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends Factory<Staff>
 */
class StaffFactory extends Factory
{
    /**
     * Definisi data default yang dibuat oleh factory ini.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // NPP 6 digit acak antara 100000 - 999999
            'npp' => $this->faker->unique()->numberBetween(100000, 999999),
            'nama' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'nomor_hp' => '08'.$this->faker->numerify('#########'),
            // Password default untuk semua dummy data adalah 'password123'
            'password' => Hash::make('password123'),
            'status' => $this->faker->randomElement(['aktif', 'nonaktif', 'keluar']),
        ];
    }
}
