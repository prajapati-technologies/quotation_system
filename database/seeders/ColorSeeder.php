<?php

namespace Database\Seeders;

use App\Models\Color;
use Illuminate\Database\Seeder;

class ColorSeeder extends Seeder
{
    public function run(): void
    {
        $brandMappings = [
            'ALUMET' => [
                'columns' => [
                    ['name' => 'MILKY WHITE', 'code' => '8911', 'type' => 'NORMAL', 'sub' => 'STANDARD (Powder Coat Colour)'],
                    ['name' => 'CHOCO FUDGE', 'code' => '8823', 'type' => 'NORMAL', 'sub' => 'STANDARD (Powder Coat Colour)'],
                    ['name' => 'SHADOW GREY', 'code' => '8859', 'type' => 'NORMAL', 'sub' => 'STANDARD (Powder Coat Colour)'],
                    ['name' => 'MIDNIGHT BLACK', 'code' => '8816', 'type' => 'NORMAL', 'sub' => 'STANDARD (Powder Coat Colour)'],
                    ['name' => 'JEWEL SAND', 'code' => '8515', 'type' => 'SPECIAL', 'sub' => 'SAHARA EFFECT (Powder Coat Colour)'],
                    ['name' => 'TRUFFLE BROWN', 'code' => '8825', 'type' => 'SPECIAL', 'sub' => 'SAHARA EFFECT (Powder Coat Colour)'],
                    ['name' => 'SAPPHIRE GREY', 'code' => '8820', 'type' => 'SPECIAL', 'sub' => 'SAHARA EFFECT (Powder Coat Colour)'],
                    ['name' => 'BLACK PEARL', 'code' => '8819', 'type' => 'SPECIAL', 'sub' => 'SAHARA EFFECT (Powder Coat Colour)'],
                    ['name' => 'MOONLIGHT SILVER', 'code' => 'E&A', 'type' => 'SPECIAL', 'sub' => 'STANDARD (Anodize color)'],
                    ['name' => 'PLATINUM', 'code' => '502', 'type' => 'SPECIAL', 'sub' => 'STANDARD (Anodize color)'],
                    ['name' => 'CHAMPAGNE', 'code' => '504', 'type' => 'SPECIAL', 'sub' => 'STANDARD (Anodize color)'],
                    ['name' => 'SUKHOTHAI GOLD', 'code' => '512', 'type' => 'SPECIAL', 'sub' => 'STANDARD (Anodize color)'],
                    ['name' => 'ONYX BLACK', 'code' => '518', 'type' => 'SPECIAL', 'sub' => 'STANDARD (Anodize color)'],
                ]
            ],
            'MUANGTHONG' => [
                'columns' => [
                    ['name' => 'MUNICH GREY', 'code' => '21080', 'type' => 'NORMAL', 'sub' => 'POWDER COATING'],
                    ['name' => 'EAGLE BLACK', 'code' => '21081', 'type' => 'NORMAL', 'sub' => 'POWDER COATING'],
                    ['name' => 'WARM GREY', 'code' => '21087', 'type' => 'NORMAL', 'sub' => 'POWDER COATING'],
                    ['name' => 'MILK CREAM', 'code' => '21809', 'type' => 'NORMAL', 'sub' => 'POWDER COATING'],
                    ['name' => 'WHITE', 'code' => '21880', 'type' => 'NORMAL', 'sub' => 'POWDER COATING'],
                    ['name' => 'SILVER', 'code' => '21880', 'type' => 'NORMAL', 'sub' => 'POWDER COATING'],
                    ['name' => 'SILVER SHM', 'code' => '21B', 'type' => 'NORMAL', 'sub' => 'POWDER COATING'],
                    ['name' => 'BROWN SAHARA', 'code' => '29U', 'type' => 'SPECIAL', 'sub' => 'ANODIZING'],
                    ['name' => 'GREY SAHARA', 'code' => '302', 'type' => 'SPECIAL', 'sub' => 'ANODIZING'],
                    ['name' => 'CONCRETE SAHARA', 'code' => '304', 'type' => 'SPECIAL', 'sub' => 'ANODIZING'],
                    ['name' => 'GREYISH SAND SAHARA', 'code' => '305', 'type' => 'SPECIAL', 'sub' => 'ANODIZING'],
                    ['name' => 'MIDNIGHT BLACK SAHARA', 'code' => '319', 'type' => 'SPECIAL', 'sub' => 'ANODIZING'],
                    ['name' => 'DARK BROWN SAHARA', 'code' => '339', 'type' => 'SPECIAL', 'sub' => 'ANODIZING'],
                    ['name' => 'INFINITY WHITE SAHARA', 'code' => '380', 'type' => 'SPECIAL', 'sub' => 'ANODIZING'],
                    ['name' => 'SILVER (ANOD)', 'code' => 'E&A', 'type' => 'SPECIAL', 'sub' => 'ANODIZING'],
                    ['name' => 'CHAMPAGNE GOLD', 'code' => '500', 'type' => 'SPECIAL', 'sub' => 'ANODIZING'],
                    ['name' => 'HERITAGE', 'code' => '512', 'type' => 'SPECIAL', 'sub' => 'ANODIZING'],
                    ['name' => 'FIRESTONE', 'code' => '514', 'type' => 'SPECIAL', 'sub' => 'ANODIZING'],
                    ['name' => 'BLACK (ANOD)', 'code' => '517', 'type' => 'SPECIAL', 'sub' => 'ANODIZING'],
                    ['name' => 'EBONY BLACK', 'code' => '519', 'type' => 'SPECIAL', 'sub' => 'ANODIZING'],
                ]
            ],
            'SMS SHIMMER' => [
                'columns' => [
                    ['name' => 'SMS WHITE', 'code' => '', 'type' => 'SPECIAL', 'sub' => 'SPECIAL/STANDARD'],
                    ['name' => 'SMS BLACK', 'code' => '', 'type' => 'SPECIAL', 'sub' => 'SPECIAL/STANDARD'],
                    ['name' => 'COLD LAVA', 'code' => '', 'type' => 'SPECIAL', 'sub' => 'SPECIAL/STANDARD'],
                    ['name' => 'WHITE SAHARA', 'code' => '', 'type' => 'SPECIAL', 'sub' => 'SPECIAL/STANDARD'],
                    ['name' => 'BLACK SAHARA', 'code' => '', 'type' => 'SPECIAL', 'sub' => 'SPECIAL/STANDARD'],
                    ['name' => 'AZTEC GREY SAHARA', 'code' => '', 'type' => 'SPECIAL', 'sub' => 'SPECIAL/STANDARD'],
                    ['name' => 'GREY SAHARA', 'code' => '', 'type' => 'SPECIAL', 'sub' => 'SPECIAL/STANDARD'],
                    ['name' => 'AQUA WHITE', 'code' => '', 'type' => 'NORMAL', 'sub' => 'NORMAL'],
                    ['name' => 'IVORY WHITE', 'code' => '', 'type' => 'NORMAL', 'sub' => 'NORMAL'],
                    ['name' => 'ICY CLOUD', 'code' => '', 'type' => 'NORMAL', 'sub' => 'NORMAL'],
                    ['name' => 'COZY GREY', 'code' => '', 'type' => 'NORMAL', 'sub' => 'NORMAL'],
                    ['name' => 'AUTUMN BROWN', 'code' => '', 'type' => 'NORMAL', 'sub' => 'NORMAL'],
                ]
            ],
            'MODA UPVC' => [
                'columns' => [
                    ['name' => 'WHITE (MODA)', 'code' => '', 'type' => 'NORMAL', 'sub' => 'MODA'],
                    ['name' => 'BLACK (MODA)', 'code' => '', 'type' => 'SPECIAL', 'sub' => 'MODA'],
                    ['name' => '2 TONE (MODA)', 'code' => '', 'type' => 'SPECIAL', 'sub' => 'MODA'],
                    ['name' => 'WOOD FINISH (MODA)', 'code' => '', 'type' => 'SPECIAL', 'sub' => 'MODA'],
                ]
            ],
        ];

        foreach ($brandMappings as $brandName => $data) {
            $brand = \App\Models\Brand::where('name', $brandName)->first();
            if (!$brand)
                continue;

            foreach ($data['columns'] as $c) {
                // Create or find category for this brand. 
                // We use 'type' (NORMAL/SPECIAL) as Name and 'sub' as Sub-category to match Excel.
                $category = \App\Models\Category::updateOrCreate([
                    'brand_id' => $brand->id,
                    'name' => $c['type'], // NORMAL or SPECIAL
                    'sub_category' => $c['sub'], // The detailed sub-category name
                    'type' => 'COLOR',
                ]);

                // Create or update color linked to category
                Color::updateOrCreate([
                    'name' => $c['name'],
                    'code' => $c['code'],
                    'category_id' => $category->id,
                ], [
                    'color_type' => $c['type'],
                    'sub_category' => $c['sub'],
                    'additional_price' => (str_contains($c['name'], 'WOOD FINISH')) ? 1000 : 0,
                ]);
            }
        }
    }
}
