<?php declare(strict_types=1);

namespace App\Services\Wolf\Strategies;

use App\Models\Product;

class IncreasingWithAgeItemStrategy implements ItemStrategy
{
    public function update(Product $product): void
    {
        // Increase quality over time
        if ($product->quality < 50) {
            $product->quality += 1;
        }

        // Decrease sell-in
        $product->sell_in -= 1;
    }
}
