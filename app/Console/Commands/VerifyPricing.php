<?php

namespace App\Console\Commands;

use App\Models\Classification;
use App\Models\Color;
use App\Models\Glass;
use App\Models\Product;
use App\Services\PricingService;
use Illuminate\Console\Command;

class VerifyPricing extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'verify:pricing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verify Pricing Logic with Seeded Data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Verifying Pricing Logic...");

        // Fetch seeded data
        $class = Classification::where('name', 'ALUMET')->first();
        if (!$class) {
            $this->error("Seeded data ALUMET not found. Run 'php artisan db:seed --class=QuotationVerificationSeeder'");
            return 1;
        }

        $prod = Product::where('classification_id', $class->id)->where('name', 'Sliding Window 2 Panels')->first();
        $color = Color::where('classification_id', $class->id)->where('name', 'Milky White')->first();
        $colorSpecial = Color::where('classification_id', $class->id)->where('name', 'Special Gold')->first();
        $glass = Glass::where('name', 'Clear Float Glass')->first();

        $service = new PricingService();

        // Test 1: Basic (Product 12950 + Color 0 + Install 700)
        $this->info("\nTest 1: Basic ALUMET Calculation");
        $result = $service->calculateItemPrice([
            'product_id' => $prod->id,
            'color_id' => $color->id,
            'quantity' => 1,
            'width' => 10,
            'height' => 10
        ]);

        $expected = 13650;
        $actual = $result['total_price'];

        if ($actual == $expected) {
            $this->info("PASS: Expected $expected, Got $actual");
        } else {
            $this->error("FAIL: Expected $expected, Got $actual");
        }

        // Test 2: Special Color (Product 12950 + Color 500 + Install 700)
        $this->info("\nTest 2: Special Color Calculation");
        $result2 = $service->calculateItemPrice([
            'product_id' => $prod->id,
            'color_id' => $colorSpecial->id,
            'quantity' => 1,
            'width' => 10,
            'height' => 10
        ]);

        $expected2 = 14150;
        $actual2 = $result2['total_price'];

        if ($actual2 == $expected2) {
            $this->info("PASS: Expected $expected2, Got $actual2");
        } else {
            $this->error("FAIL: Expected $expected2, Got $actual2");
        }

        // Test 3: With Glass
        $this->info("\nTest 3: Calculation with Glass");
        // Area = 10*10 = 100 sq ft = 9.2903 sqm
        // Glass Cost = 9.2903 * 100 = 929.03
        // Total = 12950 + 0 + 700 + 929.03 = 14579.03

        $result3 = $service->calculateItemPrice([
            'product_id' => $prod->id,
            'color_id' => $color->id, // Normal
            'glass_id' => $glass->id,
            'quantity' => 1,
            'width' => 10,
            'height' => 10
        ]);

        $actual3 = $result3['total_price'];
        $expected3 = 14579.03; // Approx

        // Allow small delta
        if (abs($actual3 - $expected3) < 0.1) {
            $this->info("PASS: Expected ~$expected3, Got $actual3");
        } else {
            $this->error("FAIL: Expected ~$expected3, Got $actual3");
        }
    }
}
