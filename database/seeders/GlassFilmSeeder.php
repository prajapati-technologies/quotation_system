<?php

namespace Database\Seeders;

use App\Models\GlassFilm;
use Illuminate\Database\Seeder;

class GlassFilmSeeder extends Seeder
{
    public function run(): void
    {
        $films = [
            '2 PLY BLACK 30',
            '2 PLY BLACK 40',
            '2 PLY BLACK 50',
            '2 PLY BLACK 60',
            '2 PLY BLACK 70',
            '2 PLY BLACK 80',
            '2 PLY BLACK 90',
            '50 GREEN',
            '60 GREEN',
            'GREEN SILVER',
            '55 BK',
            'SILVER 40',
            '40 BLUE',
            '50 BLUE',
            'BLUE SILVER',
            'GREY 35',
            'BLACK GREEN',
            'BROWN SILVER',
            'BLACK OUT',
            '2 MILL',
            '4 MILL',
            '8 MILL',
            'BLACK SILVER 20',
            '75 GREEN',
            'BLACK CHROME 10',
            'BLACK SILVER 05',
            'GOLD SILVER',
            'BLACK CHROME',
            'SILVER 10',
            '05 BK',
            'WHILE MATTE',
            'DEEP WHILE',
        ];

        foreach ($films as $name) {
            GlassFilm::updateOrCreate(['name' => $name], [
                'price_per_sqm' => 900,
            ]);
        }
    }
}
