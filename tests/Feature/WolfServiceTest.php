<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\{Product, ProductRule};
use App\Services\Wolf\WolfService;
use App\Repositories\ProductRepository;
use App\Enums\ProductCategoryEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;

class WolfServiceTest extends TestCase
{
    use RefreshDatabase;

    protected WolfService $wolfService;
    protected ProductRepository $productRepository;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a mock for the ProductRepository
        $this->productRepository = Mockery::mock(ProductRepository::class);
        $this->wolfService = new WolfService($this->productRepository);
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

        // Expect the save method to be called
        $this->productRepository->shouldReceive('save')->once()->with(Mockery::on(function ($arg) use ($product) {
            return $arg->id === $product->id && $arg->sell_in === 4 && $arg->quality === 9;
        }));

        $this->wolfService->updateProduct($product);
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

        $this->productRepository->shouldReceive('save')->once()->with(Mockery::on(function ($arg) use ($product) {
            return $arg->id === $product->id && $arg->sell_in === 4 && $arg->quality === 80; // Legendary items do not change
        }));

        $this->wolfService->updateProduct($product);
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

        $this->productRepository->shouldReceive('save')->once()->with(Mockery::on(function ($arg) use ($product) {
            return $arg->id === $product->id && $arg->sell_in === 4 && $arg->quality === 49; // Increase by 1
        }));

        $this->wolfService->updateProduct($product);
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

        $this->productRepository->shouldReceive('save')->once()->with(Mockery::on(function ($arg) use ($product) {
            return $arg->id === $product->id && $arg->sell_in === 5 && $arg->quality === 9; // Normal degradation
        }));

        $this->wolfService->updateProduct($product);
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

        $this->productRepository->shouldReceive('save')->once()->with(Mockery::on(function ($arg) use ($product) {
            return $arg->id === $product->id && $arg->sell_in === 4 && $arg->quality === 8; // Decreases by 2
        }));

        $this->wolfService->updateProduct($product);
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

        $this->productRepository->shouldReceive('save')->once()->with(Mockery::on(function ($arg) use ($product) {
            return $arg->id === $product->id && $arg->sell_in === -1 && $arg->quality === 0; // Drops to zero
        }));

        $this->wolfService->updateProduct($product);
    }
}
