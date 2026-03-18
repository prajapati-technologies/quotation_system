<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            'Sliding Window 2 panels',
            'Sliding Window 4 panels',
            'Sliding Doors 2 panel',
            'Sliding Doors 4 panel',
            'Opening window signle',
            'Opening window 2 panels',
            'Opening Door signle',
            'Opening Door 2 panels',
            'Fix window',
            'Opening mosquito netting m2',
            'Sliding mosquito netting m2',
            'Folding mosquito netting m2',
            'Sliding stainless mosquito netting m2',
        ];

        foreach ($products as $name) {
            Product::updateOrCreate(['name' => $name]);
        }
    }
}
