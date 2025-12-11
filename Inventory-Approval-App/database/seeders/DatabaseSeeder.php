<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Panggil seeder lain di sini
        $this->call([
            // UserSeeder::class, // Jika Anda memisahkan user seeder
            InventorySeeder::class, // <-- Pastikan ini ada
            UserSeeder::class
        ]);
    }
}