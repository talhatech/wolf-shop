<?php declare(strict_types=1);

namespace App\Services\Wolf\Strategies;

use App\Models\Product;

class NormalItemStrategy implements ItemStrategy
{
    public function update(Product $product): void
    {
        // Decrease quality
        $degradation = $product->sell_in <= 0 ? 2 : 1;
        $product->quality = max(0, $product->quality - $degradation);

        // Decrease sell-in
        $product->sell_in -= 1;
    }
}
