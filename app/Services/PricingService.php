<?php

namespace App\Services;

use App\Models\BrandRate;
use App\Models\Color;
use App\Models\Glass;
use App\Models\GlassFilm;
use App\Models\Accessory;

class PricingService
{
    /**
     * Calculate strict item price based on BrandRate + Color + Installation + Glass + Film + Accessories
     */
    public function calculateItemPrice(array $data): array
    {
        $details = [];

        // Dimensions & Area (mm -> Sqm)
        $width = floatval($data['width'] ?? 0);
        $height = floatval($data['height'] ?? 0);

        // Area in Sqm (mm -> m, then m²)
        $area = ($width / 1000) * ($height / 1000);

        $details['calculated_area_sqm'] = number_format($area, 3);
        $details['applied_area_sqm'] = number_format($area, 3);

        // 1. Base Price (Brand Rate) - NOW AREA BASED
        $brandId = $data['brand_id'] ?? null;
        $productId = $data['product_id'] ?? null;
        $classification = $data['classification'] ?? 'NORMAL';

        $baseRate = 0;      // Brand rate per Sqm
        $installation = 0;  // Installation per item (we'll treat as part of per Sqm package)

        if ($brandId && $productId) {
            $brandRate = BrandRate::where('brand_id', $brandId)
                ->where('product_id', $productId)
                ->first();

            if ($brandRate) {
                $baseRate = ($classification === 'SPECIAL') ? $brandRate->special_price : $brandRate->normal_price;
                $installation = $brandRate->installation_price;
            }
        }

        // Build a unified rate per Sqm (includes base, color, glass, accessories, installation, etc.)
        $ratePerSqm = 0.0;

        // Base rate already per Sqm
        $ratePerSqm += $baseRate;
        $details['base_rate_per_sqm'] = $baseRate;

        // 2. Color Additional Price (treat as per Sqm add-on)
        $colorId = $data['color_id'] ?? null;
        if ($colorId) {
            $color = Color::find($colorId);
            if ($color) {
                $ratePerSqm += $color->additional_price;
                $details['color_rate_per_sqm'] = $color->additional_price;
            }
        }

        // 3. Glass Price (per Sqm)
        $glassId = $data['glass_id'] ?? null;
        if ($glassId) {
            $glass = Glass::find($glassId);
            if ($glass) {
                $ratePerSqm += $glass->price_per_sqm;
                $details['glass_rate_per_sqm'] = $glass->price_per_sqm;
            }
        }

        // 4. Glass Film Price (per Sqm) - kept for backward compatibility if provided
        $glassFilmId = $data['glass_film_id'] ?? null;
        if ($glassFilmId) {
            $film = GlassFilm::find($glassFilmId);
            if ($film) {
                $ratePerSqm += $film->price_per_sqm;
                $details['film_rate_per_sqm'] = $film->price_per_sqm;
            }
        }

        // 5. Accessories Price (treat sum as per Sqm add-on for simplicity)
        $accessoryIds = $data['accessories'] ?? [];
        if (!is_array($accessoryIds)) {
            $accessoryIds = [];
        }
        $accessoriesCost = 0;
        if (!empty($accessoryIds)) {
            $accessoriesCost = Accessory::whereIn('id', $accessoryIds)->sum('price');
            $ratePerSqm += $accessoriesCost;
            $details['accessories_rate_per_sqm'] = $accessoriesCost;
        }

        // 6. Installation (treat as per Sqm add-on so total scales linearly with area)
        $finalInstallation = isset($data['installation_cost']) && $data['installation_cost'] !== ''
            ? floatval($data['installation_cost'])
            : $installation;

        $ratePerSqm += $finalInstallation;
        $details['installation_rate_per_sqm'] = $finalInstallation;

        // Final unit price and total price
        // Core rule: item_total = area * rate_per_sqm
        $goodsRatePerSqm = $ratePerSqm - $finalInstallation;
        
        $unitPrice = $area * $ratePerSqm;

        $quantity = intval($data['quantity'] ?? 1);
        $total = $unitPrice * $quantity;

        $details['rate_per_sqm'] = number_format($ratePerSqm, 4, '.', '');
        $details['unit_price'] = number_format($unitPrice, 4, '.', '');

        return [
            'unit_price' => $unitPrice,
            'total_price' => $total,
            'goods_rate_per_sqm' => $goodsRatePerSqm,
            'installation_rate_per_sqm' => $finalInstallation,
            'details' => $details,
            'calculated_installation' => $installation,
        ];
    }
}
