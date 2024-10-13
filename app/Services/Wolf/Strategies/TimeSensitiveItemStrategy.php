<?php declare(strict_types=1);

namespace App\Services\Wolf\Strategies;

use App\Models\Product;

class TimeSensitiveItemStrategy implements ItemStrategy
{
    public function update(Product $product): void
    {
        // Increase quality based on sell-in proximity
        if ($product->sell_in > 10) {
            $product->quality += 1;
        } elseif ($product->sell_in > 5) {
            $product->quality += 2;
        } elseif ($product->sell_in > 0) {
            $product->quality += 3;
        } else {
            $product->quality = 0; // Quality drops to 0 after the sell-in date
        }

        // Ensure quality does not exceed 50
        $product->quality = min(50, $product->quality);

        // Decrease sell-in
        $product->sell_in -= 1;
    }
}
