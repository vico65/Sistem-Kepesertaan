<?php

namespace Database\Seeders;

/**
 * DatabaseSeeder
 *
 * Tujuan    : Menjalankan seluruh seeder aplikasi saat php artisan db:seed dijalankan.
 * Diubah    : 2026-10-27
 * Perubahan : Mendaftarkan StaffSeeder untuk mengisi data dummy staff
 */

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Panggil StaffSeeder untuk mengisi 10 data dummy staff
        $this->call([
            StaffSeeder::class,
        ]);
    }
}
