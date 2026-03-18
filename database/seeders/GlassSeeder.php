<?php

namespace Database\Seeders;

use App\Models\Glass;
use Illuminate\Database\Seeder;

class GlassSeeder extends Seeder
{
    public function run(): void
    {
        $glasses = [
            // Standard Single (Green)
            ['name' => 'Float Glass Green 5 mm', 'thickness' => '5 mm'],
            ['name' => 'Float Glass Green 6 mm', 'thickness' => '6 mm'],
            ['name' => 'Float Glass Green 8 mm', 'thickness' => '8 mm'],
            ['name' => 'Float Glass Green 10 mm', 'thickness' => '10 mm'],
            // Standard Single (Black)
            ['name' => 'Float Glass Black 5 mm', 'thickness' => '5 mm'],
            ['name' => 'Float Glass Black 6 mm', 'thickness' => '6 mm'],
            ['name' => 'Float Glass Black 8 mm', 'thickness' => '8 mm'],
            ['name' => 'Float Glass Black 10 mm', 'thickness' => '10 mm'],
            // Standard Single (Clear)
            ['name' => 'Float Glass Clear 5 mm', 'thickness' => '5 mm'],
            ['name' => 'Float Glass Clear 6 mm', 'thickness' => '6 mm'],
            ['name' => 'Float Glass Clear 8 mm', 'thickness' => '8 mm'],
            ['name' => 'Float Glass Clear 10 mm', 'thickness' => '10 mm'],

            // Frosted / Tinted / Reflective
            ['name' => 'Frosted Glass 5 mm', 'thickness' => '5 mm'],
            ['name' => 'Green Tinted 5 mm', 'thickness' => '5 mm'],
            ['name' => 'Reflective Glass Clear 5 mm', 'thickness' => '5 mm'],
            ['name' => 'Frosted Glass Green 6 mm', 'thickness' => '6 mm'],
            ['name' => 'Green Tinted Green 6 mm', 'thickness' => '6 mm'],
            ['name' => 'Reflective Glass Clear 6 mm', 'thickness' => '6 mm'],

            // Laminated
            ['name' => 'Laminated 5+5 mm', 'thickness' => '5 + 5 mm'],
            ['name' => 'Laminated 6+6 mm', 'thickness' => '6 + 6 mm'],
            ['name' => 'Laminated 8+8 mm', 'thickness' => '8 + 8 mm'],

            // IGU
            ['name' => 'IGU 5+6+6 mm', 'thickness' => '5 + 6 + 6'],
            ['name' => 'IGU 5+8+6 mm', 'thickness' => '5 + 8 + 6'],
            ['name' => 'IGU + Laminated 4+8+5 mm', 'thickness' => '4 + 8 + 5'],

            // Bulletproof
            ['name' => 'Bullet Resistant 19 mm', 'thickness' => '19 mm'],
            ['name' => 'Bullet Resistant 24 mm', 'thickness' => '24 mm'],
        ];

        foreach ($glasses as $g) {
            Glass::updateOrCreate(['name' => $g['name']], [
                'thickness' => $g['thickness'],
                'price_per_sqm' => 0,
            ]);
        }
    }
}
