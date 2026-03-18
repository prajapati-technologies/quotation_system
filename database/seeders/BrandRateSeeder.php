<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\BrandRate;
use App\Models\Product;
use Illuminate\Database\Seeder;

class BrandRateSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'ALUMET' => [
                'Sliding Window 2 panels' => ['normal' => 12950, 'special' => 13450, 'installation' => 700],
                'Sliding Window 4 panels' => ['normal' => 12950, 'special' => 13450, 'installation' => 700],
                'Sliding Doors 2 panel' => ['normal' => 12950, 'special' => 13450, 'installation' => 700],
                'Sliding Doors 4 panel' => ['normal' => 12950, 'special' => 13450, 'installation' => 700],
                'Opening window signle' => ['normal' => 12950, 'special' => 13450, 'installation' => 700],
                'Opening window 2 panels' => ['normal' => 12950, 'special' => 13450, 'installation' => 700],
                'Opening Door signle' => ['normal' => 15050, 'special' => 15550, 'installation' => 700],
                'Opening Door 2 panels' => ['normal' => 15050, 'special' => 15550, 'installation' => 700],
                'Fix window' => ['normal' => 8500, 'special' => 9000, 'installation' => 700],
                'Sliding mosquito netting m2' => ['normal' => 3000, 'special' => 3400, 'installation' => 0],
                'Folding mosquito netting m2' => ['normal' => 3600, 'special' => 4000, 'installation' => 0],
                'Sliding stainless mosquito netting m2' => ['normal' => 6000, 'special' => 7000, 'installation' => 0],
            ],
            'MUANGTHONG' => [
                'Sliding Window 2 panels' => ['normal' => 12950, 'special' => 13450, 'installation' => 700],
                'Sliding Window 4 panels' => ['normal' => 12950, 'special' => 13450, 'installation' => 700],
                'Sliding Doors 2 panel' => ['normal' => 12950, 'special' => 13450, 'installation' => 700],
                'Sliding Doors 4 panel' => ['normal' => 12950, 'special' => 13450, 'installation' => 700],
                'Opening window signle' => ['normal' => 12950, 'special' => 13450, 'installation' => 700],
                'Opening window 2 panels' => ['normal' => 12950, 'special' => 13450, 'installation' => 700],
                'Opening Door signle' => ['normal' => 15050, 'special' => 15550, 'installation' => 700],
                'Opening Door 2 panels' => ['normal' => 15050, 'special' => 15550, 'installation' => 700],
                'Fix window' => ['normal' => 8500, 'special' => 9000, 'installation' => 700],
                'Sliding mosquito netting m2' => ['normal' => 3000, 'special' => 3400, 'installation' => 0],
                'Folding mosquito netting m2' => ['normal' => 3600, 'special' => 4000, 'installation' => 0],
                'Sliding stainless mosquito netting m2' => ['normal' => 6000, 'special' => 7000, 'installation' => 0],
            ],
            'SMS SHIMMER' => [
                'Sliding Window 2 panels' => ['normal' => 9950, 'special' => 10450, 'installation' => 700],
                'Sliding Window 4 panels' => ['normal' => 9950, 'special' => 10450, 'installation' => 700],
                'Sliding Doors 2 panel' => ['normal' => 9950, 'special' => 10450, 'installation' => 700],
                'Sliding Doors 4 panel' => ['normal' => 9950, 'special' => 10450, 'installation' => 700],
                'Opening window signle' => ['normal' => 9950, 'special' => 10450, 'installation' => 700],
                'Opening window 2 panels' => ['normal' => 9950, 'special' => 10450, 'installation' => 700],
                'Opening Door signle' => ['normal' => 12050, 'special' => 12550, 'installation' => 700],
                'Opening Door 2 panels' => ['normal' => 12050, 'special' => 12550, 'installation' => 700],
                'Fix window' => ['normal' => 5500, 'special' => 6000, 'installation' => 700],
                'Sliding mosquito netting m2' => ['normal' => 3000, 'special' => 3400, 'installation' => 0],
                'Folding mosquito netting m2' => ['normal' => 3600, 'special' => 4000, 'installation' => 0],
            ],
            'STANDARD' => [
                'Sliding Window 2 panels' => ['normal' => 4000, 'special' => 4500, 'installation' => 400],
                'Sliding Window 4 panels' => ['normal' => 4000, 'special' => 4500, 'installation' => 400],
                'Sliding Doors 2 panel' => ['normal' => 4000, 'special' => 4500, 'installation' => 400],
                'Sliding Doors 4 panel' => ['normal' => 4000, 'special' => 4500, 'installation' => 400],
                'Opening window signle' => ['normal' => 4500, 'special' => 5000, 'installation' => 400],
                'Opening window 2 panels' => ['normal' => 4500, 'special' => 5000, 'installation' => 400],
                'Opening Door signle' => ['normal' => 7500, 'special' => 8000, 'installation' => 400],
                'Opening Door 2 panels' => ['normal' => 7500, 'special' => 8000, 'installation' => 400],
                'Fix window' => ['normal' => 3500, 'special' => 4000, 'installation' => 400],
                'Sliding mosquito netting m2' => ['normal' => 1500, 'special' => 1800, 'installation' => 0],
                'Folding mosquito netting m2' => ['normal' => 3600, 'special' => 4000, 'installation' => 0],
            ],
            'MODA UPVC' => [
                'Sliding Window 2 panels' => ['normal' => 9450, 'special' => 10950, 'installation' => 700],
                'Sliding Window 4 panels' => ['normal' => 9450, 'special' => 10950, 'installation' => 700],
                'Sliding Doors 2 panel' => ['normal' => 9450, 'special' => 10950, 'installation' => 700],
                'Sliding Doors 4 panel' => ['normal' => 9450, 'special' => 10950, 'installation' => 700],
                'Opening window signle' => ['normal' => 9450, 'special' => 10950, 'installation' => 700],
                'Opening window 2 panels' => ['normal' => 9450, 'special' => 10950, 'installation' => 700],
                'Opening Door signle' => ['normal' => 11550, 'special' => 13050, 'installation' => 700],
                'Opening Door 2 panels' => ['normal' => 11550, 'special' => 13050, 'installation' => 700],
                'Fix window' => ['normal' => 6300, 'special' => 7800, 'installation' => 700],
                'Opening mosquito netting m2' => ['normal' => 1500, 'special' => 1500, 'installation' => 0],
                'Sliding mosquito netting m2' => ['normal' => 2500, 'special' => 2500, 'installation' => 0],
                'Folding mosquito netting m2' => ['normal' => 3600, 'special' => 4000, 'installation' => 200],
            ],
        ];

        foreach ($data as $brandName => $products) {
            $brand = Brand::where('name', $brandName)->first();
            if ($brand) {
                foreach ($products as $productName => $rates) {
                    $product = Product::where('name', $productName)->first();
                    if ($product) {
                        BrandRate::updateOrCreate([
                            'brand_id' => $brand->id,
                            'product_id' => $product->id,
                        ], [
                            'normal_price' => $rates['normal'],
                            'special_price' => $rates['special'],
                            'installation_price' => $rates['installation'],
                        ]);
                    }
                }
            }
        }
    }
}
