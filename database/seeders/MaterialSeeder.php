<?php

namespace Database\Seeders;

use App\Models\Material;
use Illuminate\Database\Seeder;

class MaterialSeeder extends Seeder
{
    public function run(): void
    {
        $materials = [
            'Aluminium',
            'UPVC',
        ];

        foreach ($materials as $name) {
            Material::updateOrCreate(['name' => $name]);
        }
    }
}
