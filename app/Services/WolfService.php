<?php

namespace App\Services;

use App\Models\Product;
use App\Enums\ProductCategoryEnum;

final class WolfService
{
    public function updateProduct(Product $product): void
    {
        $rule = $product->rule;

        // Decrease sell_in daily
        $this->decreaseSellIn($product, 1); // 1 = one day

        // Apply the rule logic
        $this->updateQuality($product, $rule);

        // After applying the rule, persist the updated product data
        $product->save();
    }

    /**
     * Applies the product rule to the product.
     */
    private function updateQuality(Product &$product, $rule): void
    {
        // Handle quality adjustments based on rule constraints
        switch ($rule->category) {
            case ProductCategoryEnum::NORMAL:
                // Quality degrades twice as fast when sell_in is negative
                $degradationFactor = $product->sell_in < 0 ? 2 : 1;
                $this->decreaseQuality($product, $rule->daily_decrease * $degradationFactor, $rule->min_quality);
                break;

            case ProductCategoryEnum::LEGENDARY:
                // that Legendary items don't change in quality :-)
                break;

            case ProductCategoryEnum::INCREASING_WITH_AGE:
                // Special handling for items that increase in quality over time
                $this->increaseQuality($product, 1, $rule->max_quality); // Normal increment for increasing items
                break;

            case ProductCategoryEnum::TIME_SENSITIVE:
                // Handle time-sensitive items that have specific quality changes based on SellIn
                if ($product->sell_in <= 0) {
                    $product->quality = 0; // Drops to zero after SellIn date
                } elseif ($product->sell_in <= 5) {
                    $this->increaseQuality($product, 3, $rule->max_quality); // Increase quality by 3
                } elseif ($product->sell_in <= 10) {
                    $this->increaseQuality($product, 2, $rule->max_quality); // Increase quality by 2
                } else {
                    // Normal degradation for other cases
                    $this->decreaseQuality($product, $rule->daily_decrease, $rule->min_quality);
                }
                break;

            case ProductCategoryEnum::CONJURED:
                // Items that degrade twice as fast
                $this->decreaseQuality($product, $rule->daily_decrease * 2, $rule->min_quality);
                break;

            default:
                // Fallback for unrecognized categories
                $this->decreaseQuality($product, $rule->daily_decrease, $rule->min_quality);
                break;
        }
    }

    /**
     * Increase the product's quality but ensure it does not exceed max_quality.
     */
    private function increaseQuality(Product &$product, int $value, int $maxQuality): void
    {
        $product->quality = min($product->quality + $value, $maxQuality);
    }

    /**
     * Decrease the product's quality but ensure it does not go below min_quality.
     */
    private function decreaseQuality(Product &$product, int $value, int $minQuality): void
    {
        $product->quality = max($product->quality - $value, $minQuality);
    }

    /**
     * Decrease the product's sell-in value by the given amount.
     */
    private function decreaseSellIn(Product &$product, int $value): void
    {
        $product->sell_in -= $value;
    }
}
