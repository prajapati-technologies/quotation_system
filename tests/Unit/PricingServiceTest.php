<?php

namespace Tests\Unit;

use App\Models\Classification;
use App\Models\Color;
use App\Models\Glass;
use App\Models\Material;
use App\Models\Product;
use App\Services\PricingService;
use Tests\TestCase;

class PricingServiceTest extends TestCase
{
    // removed RefreshDatabase to use existing seeded DB

    public function test_calculate_item_price_alumet()
    {
        // Fetch seeded data
        $class = Classification::where('name', 'ALUMET')->first();
        if (!$class) {
            $this->markTestSkipped('Seeded data ALUMET not found.');
        }

        $prod = Product::where('classification_id', $class->id)->where('name', 'Sliding Window 2 Panels')->first();
        $color = Color::where('classification_id', $class->id)->where('name', 'Milky White')->first();
        $colorSpecial = Color::where('classification_id', $class->id)->where('name', 'Special Gold')->first();

        $service = new PricingService();

        // Test 1: Basic
        $result = $service->calculateItemPrice([
            'product_id' => $prod->id,
            'color_id' => $color->id,
            'quantity' => 1,
            'width' => 10,
            'height' => 10
        ]);

        // 12950 (Product) + 0 (Color) + 700 (Install) = 13650
        $this->assertEquals(13650, $result['total_price']);

        // Test 2: Special Color (+500)
        $result2 = $service->calculateItemPrice([
            'product_id' => $prod->id,
            'color_id' => $colorSpecial->id,
            'quantity' => 1,
            'width' => 10,
            'height' => 10
        ]);
        // 12950 + 500 + 700 = 14150
        $this->assertEquals(14150, $result2['total_price']);
    }

    public function test_calculate_with_glass()
    {
        $glass = Glass::where('name', 'Clear Float Glass')->first();
        if (!$glass) {
            $this->markTestSkipped('Seeded glass not found.');
        }

        // We need a product and color to pass to service, reusing above or fetching
        $class = Classification::where('name', 'ALUMET')->first();
        $prod = Product::where('classification_id', $class->id)->first();

        $service = new PricingService();

        $widthFt = 10;
        $heightFt = 10;
        $areaSqFt = 100;
        $areaSqm = $areaSqFt * 0.092903; // ~9.29 sqm
        $expectedGlassCost = $areaSqm * $glass->price_per_sqm; // 9.29 * 100 = 929.03?
        // Wait, what did I seed glass price as? 
        // In seeder: 'price_per_sqm' => 100

        // Product (12950) + Install (700) + Glass (~929)
        // Let's just check if glass cost is included correctly in the delta

        $result = $service->calculateItemPrice([
            'product_id' => $prod->id,
            'glass_id' => $glass->id,
            'width' => $widthFt,
            'height' => $heightFt,
            'quantity' => 1
        ]);

        // Base without glass
        $baseResult = $service->calculateItemPrice([
            'product_id' => $prod->id,
            'width' => $widthFt,
            'height' => $heightFt,
            'quantity' => 1
        ]);

        $diff = $result['total_price'] - $baseResult['total_price'];

        $this->assertEqualsWithDelta($expectedGlassCost, $diff, 0.1);
    }
}
