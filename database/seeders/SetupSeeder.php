<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Material;
use App\Models\MaterialType;
use App\Models\Brand;
use App\Models\Product;
use App\Models\BrandRate;
use App\Models\Category;
use App\Models\Color;
use App\Models\User;

class SetupSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Materials
        $alu = Material::create(['name' => 'Aluminium']);
        $upvc = Material::create(['name' => 'UPVC']);

        // 2. Material Types
        MaterialType::create(['material_id' => $alu->id, 'name' => 'Premium Euro Aluminium']);
        MaterialType::create(['material_id' => $alu->id, 'name' => 'Premium Semi Euro Aluminium']);
        MaterialType::create(['material_id' => $alu->id, 'name' => 'Standard Aluminium']);

        MaterialType::create(['material_id' => $upvc->id, 'name' => 'Moda Premium UPVC']);

        // 3. Brands (Rate Sheets) linked to Material
        $alumet = Brand::create(['material_id' => $alu->id, 'name' => 'ALUMET']);
        $muangthong = Brand::create(['material_id' => $alu->id, 'name' => 'MUANGTHONG']);
        $sms = Brand::create(['material_id' => $alu->id, 'name' => 'SMS SCHIMMER']);

        $moda = Brand::create(['material_id' => $upvc->id, 'name' => 'MODA UPVC']);

        // 4. Products (Global list)
        $productNames = [
            'Sliding Window 2 Panels',
            'Sliding Window 4 Panels',
            'Sliding Door 2 Panels',
            'Sliding Door 4 Panels',
            'Opening Window Single',
            'Opening Window 2 Panels',
            'Opening Door Single',
            'Opening Door 2 Panels',
            'Fix Window',
        ];

        $products = [];
        foreach ($productNames as $name) {
            $products[] = Product::create(['name' => $name]);
        }

        // 5. Brand Rates (Pricing)
        // Seeding dummy prices for all brand-product combinations
        $brands = [$alumet, $muangthong, $sms, $moda];

        foreach ($brands as $brand) {
            foreach ($products as $product) {
                BrandRate::create([
                    'brand_id' => $brand->id,
                    'product_id' => $product->id,
                    'normal_price' => rand(1000, 5000),
                    'special_price' => rand(5500, 8000),
                    'installation_price' => 500 + rand(0, 5) * 100,
                ]);
            }
        }

        // 6. Categories (Color Groups)
        $catStandardPowder = Category::create(['name' => 'STANDARD (Powder Coat Colour)']);
        $catSahara = Category::create(['name' => 'SAHARA EFFECT (Powder Coat Colour)']);
        $catAnodize = Category::create(['name' => 'STANDARD (Anodize Color)']);

        // 7. Colors
        // Standard (Normal)
        Color::create(['category_id' => $catStandardPowder->id, 'name' => 'White', 'color_type' => 'NORMAL', 'additional_price' => 0]);
        Color::create(['category_id' => $catStandardPowder->id, 'name' => 'Black', 'color_type' => 'NORMAL', 'additional_price' => 0]);

        // Sahara (Special?) - Assuming Special for demo, or maybe Normal but different category
        // The spec implies: IF color_type = SPECIAL -> additional_price > 0
        // Let's make Sahara Special
        Color::create(['category_id' => $catSahara->id, 'name' => 'Sahara Grey', 'color_type' => 'SPECIAL', 'additional_price' => 500]);
        Color::create(['category_id' => $catSahara->id, 'name' => 'Sahara Brown', 'color_type' => 'SPECIAL', 'additional_price' => 500]);

        // Anodize (Normal/Special?) - Let's mix
        Color::create(['category_id' => $catAnodize->id, 'name' => 'Natural Anodize', 'color_type' => 'NORMAL', 'additional_price' => 0]);
        Color::create(['category_id' => $catAnodize->id, 'name' => 'Champagne', 'color_type' => 'SPECIAL', 'additional_price' => 800]);

        // 8. Users
        if (!User::where('email', 'admin@example.com')->exists()) {
            User::create([
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'role' => 'admin'
            ]);
        }
    }
}