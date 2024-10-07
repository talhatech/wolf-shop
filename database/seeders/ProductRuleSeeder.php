<?php

namespace Database\Seeders;

use App\Enums\ProductCategoryEnum;
use Illuminate\Support\Str;
use App\Models\ProductCategory;
use App\Models\ProductRule;
use Illuminate\Database\Seeder;

class ProductRuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rules = []; // Initialize the rules array

        foreach (ProductCategoryEnum::getValues() as $categoryValue) {
            $rules[] = [
                'id' => Str::uuid(),
                'category' => $categoryValue,
                'min_quality' => 0,
                'max_quality' => $this->getMaxQuality($categoryValue),
                'daily_decrease' => $this->getDailyDecrease($categoryValue),
                'sell_in_threshold' => $this->getSellInThreshold($categoryValue),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        ProductRule::insert($rules);
    }

    private function getMaxQuality(string $categoryName): int
    {
        return $categoryName === ProductCategoryEnum::LEGENDARY ? 80 : 50;
    }

    private function getDailyDecrease(string $categoryName): int
    {
        return match ($categoryName) {
            ProductCategoryEnum::INCREASING_WITH_AGE => 0,
            ProductCategoryEnum::TIME_SENSITIVE => 0,
            ProductCategoryEnum::CONJURED => 2,
            default => 1,
        };
    }

    private function getSellInThreshold(string $categoryName): ?int
    {
        // todo: concert condition -> move to setting
        return $categoryName === ProductCategoryEnum::TIME_SENSITIVE ? 10 : null;
    }
}
