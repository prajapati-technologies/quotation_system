<?php

namespace Database\Seeders;

use App\Models\Classification;
use App\Models\Color;
use App\Models\Glass;
use App\Models\Material;
use App\Models\Product;
use Illuminate\Database\Seeder;

class QuotationVerificationSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Material
        $mat = Material::create([
            'name' => 'Premium Euro Aluminium',
            'type' => 'Aluminium',
            'base_price' => 0 // Base price might not be used if Product price is absolute
        ]);

        // 2. Classification
        $class = Classification::create([
            'material_id' => $mat->id,
            'name' => 'ALUMET',
            'description' => 'Premium Brand'
        ]);

        // 3. Product
        $prod = Product::create([
            'classification_id' => $class->id,
            'name' => 'Sliding Window 2 Panels',
            'price' => 12950,
            'has_installation' => true,
            'installation_price' => 700
        ]);

        // 4. Color (Normal)
        $colorNormal = Color::create([
            'classification_id' => $class->id,
            'name' => 'Milky White',
            'code' => '8911',
            'category' => 'NORMAL',
            'additional_price' => 0
        ]);

        // 5. Color (Special)
        $colorSpecial = Color::create([
            'classification_id' => $class->id,
            'name' => 'Special Gold',
            'code' => '9999',
            'category' => 'SPECIAL',
            'additional_price' => 500 // 12950 + 500 = 13450
        ]);

        // 6. Glass
        $glass = Glass::create([
            'name' => 'Clear Float Glass',
            'price_per_sqm' => 100, // Example
            'thickness' => '6mm'
        ]);

        $this->command->info("Seeded verification data.");
    }
}
