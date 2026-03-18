<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Material;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $mapping = [
            'Aluminium' => ['ALUMET', 'MUANGTHONG', 'SMS SHIMMER', 'STANDARD'],
            'UPVC' => ['MODA UPVC'],
        ];

        foreach ($mapping as $materialName => $brands) {
            $material = Material::where('name', $materialName)->first();
            if ($material) {
                foreach ($brands as $brandName) {
                    Brand::updateOrCreate([
                        'material_id' => $material->id,
                        'name' => $brandName,
                    ]);
                }
            }
        }
    }
}
