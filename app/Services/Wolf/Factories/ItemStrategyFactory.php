<?php declare(strict_types=1);

namespace App\Services\Wolf\Factories;

use App\Models\Product;
use App\Enums\ProductCategoryEnum;
use App\Services\Wolf\Strategies\{
    NormalItemStrategy,
    LegendaryItemStrategy,
    IncreasingWithAgeItemStrategy,
    TimeSensitiveItemStrategy,
    ConjuredItemStrategy,
    ItemStrategy
};

class ItemStrategyFactory
{
    public static function create(Product $product): ItemStrategy
    {
        return match ($product?->rule->category) {
            ProductCategoryEnum::LEGENDARY => new LegendaryItemStrategy(),
            ProductCategoryEnum::INCREASING_WITH_AGE => new IncreasingWithAgeItemStrategy(),
            ProductCategoryEnum::TIME_SENSITIVE => new TimeSensitiveItemStrategy(),
            ProductCategoryEnum::CONJURED => new ConjuredItemStrategy(),
            default => new NormalItemStrategy(),
        };
    }
}
