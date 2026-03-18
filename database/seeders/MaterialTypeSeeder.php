<?php

namespace Database\Seeders;

use App\Models\Material;
use App\Models\MaterialType;
use Illuminate\Database\Seeder;

class MaterialTypeSeeder extends Seeder
{
    public function run(): void
    {
        $alu = Material::where('name', 'Aluminium')->first();
        $upvc = Material::where('name', 'UPVC')->first();

        if ($alu) {
            MaterialType::updateOrCreate(['material_id' => $alu->id, 'name' => 'Premium Euro Aluminium']);
            MaterialType::updateOrCreate(['material_id' => $alu->id, 'name' => 'Premium Semi Euro Aluminium']);
            MaterialType::updateOrCreate(['material_id' => $alu->id, 'name' => 'Standard Aluminium']);
        }

        if ($upvc) {
            MaterialType::updateOrCreate(['material_id' => $upvc->id, 'name' => 'Moda Premium UPVC']);
        }
    }
}
