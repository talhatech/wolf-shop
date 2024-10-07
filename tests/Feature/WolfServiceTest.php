<?php

namespace Tests\Feature;


use Tests\TestCase;
use App\Models\{Product,ProductRule};
use App\Services\WolfService;
use App\Enums\ProductCategoryEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WolfServiceTest extends TestCase
{
    use RefreshDatabase;

    protected WolfService $wolfService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->wolfService = new WolfService();
    }

    public function testUpdateProductNormal()
    {
        $productRule = ProductRule::factory()->create([
            'category' => ProductCategoryEnum::NORMAL,
            'daily_decrease' => 1,
            'min_quality' => 0,
        ]);

        $product = Product::factory()->create([
            'quality' => 10,
            'sell_in' => 5,
            'rule_id' => $productRule->id,
        ]);

        $this->wolfService->updateProduct($product);

        $this->assertEquals(4, $product->sell_in);
        $this->assertEquals(9, $product->quality);
    }

    public function testUpdateProductLegendary()
    {
        $productRule = ProductRule::factory()->create([
            'category' => ProductCategoryEnum::LEGENDARY,
            'daily_decrease' => 0,
            'min_quality' => 80,
        ]);

        $product = Product::factory()->create([
            'quality' => 80,
            'sell_in' => 5,
            'rule_id' => $productRule->id,
        ]);

        $this->wolfService->updateProduct($product);

        $this->assertEquals(4, $product->sell_in);
        $this->assertEquals(80, $product->quality); // Legendary items do not change
    }

    public function testUpdateProductIncreasingWithAge()
    {
        $productRule = ProductRule::factory()->create([
            'category' => ProductCategoryEnum::INCREASING_WITH_AGE,
            'daily_decrease' => 1,
            'max_quality' => 50,
        ]);

        $product = Product::factory()->create([
            'quality' => 48,
            'sell_in' => 5,
            'rule_id' => $productRule->id,
        ]);

        $this->wolfService->updateProduct($product);

        $this->assertEquals(4, $product->sell_in);
        $this->assertEquals(49, $product->quality); // Increase by 1
    }

    public function testUpdateProductTimeSensitive()
    {
        $productRule = ProductRule::factory()->create([
            'category' => ProductCategoryEnum::TIME_SENSITIVE,
            'daily_decrease' => 1,
            'max_quality' => 50,
        ]);

        $product = Product::factory()->create([
            'quality' => 10,
            'sell_in' => 6,
            'rule_id' => $productRule->id,
        ]);

        $this->wolfService->updateProduct($product);

        $this->assertEquals(5, $product->sell_in);
        $this->assertEquals(9, $product->quality); // Normal degradation
    }

    public function testUpdateProductConjured()
    {
        $productRule = ProductRule::factory()->create([
            'category' => ProductCategoryEnum::CONJURED,
            'daily_decrease' => 2,
            'min_quality' => 0,
        ]);

        $product = Product::factory()->create([
            'quality' => 10,
            'sell_in' => 5,
            'rule_id' => $productRule->id,
        ]);

        $this->wolfService->updateProduct($product);

        $this->assertEquals(4, $product->sell_in);
        $this->assertEquals(8, $product->quality); // Decreases by 2
    }

    public function testUpdateProductTimeSensitiveQualityDropsToZeroAfterSellInDate()
    {
        $productRule = ProductRule::factory()->create([
            'category' => ProductCategoryEnum::TIME_SENSITIVE,
            'daily_decrease' => 1,
            'max_quality' => 50,
        ]);

        $product = Product::factory()->create([
            'quality' => 10,
            'sell_in' => 0,
            'rule_id' => $productRule->id,
        ]);

        $this->wolfService->updateProduct($product);

        $this->assertEquals(-1, $product->sell_in);
        $this->assertEquals(0, $product->quality); // Drops to zero
    }
}
