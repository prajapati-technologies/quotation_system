<?php

namespace Database\Seeders;

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
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'admin@vyapariq.com'],
            [
                'name' => 'Vyapari Admin',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        $this->call([
            MaterialSeeder::class,
            MaterialTypeSeeder::class,
            BrandSeeder::class,
            ProductSeeder::class,
            BrandRateSeeder::class,
            ColorSeeder::class,
            GlassSeeder::class,
            GlassFilmSeeder::class,
        ]);
    }
}
