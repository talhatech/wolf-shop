<?php declare(strict_types=1);

namespace App\Services\Wolf\Strategies;

use App\Models\Product;

class ConjuredItemStrategy implements ItemStrategy
{
    public function update(Product $product): void
    {
        // Decrease quality twice as fast
        $degradation = $product->sell_in <= 0 ? 4 : 2;
        $product->quality = max(0, $product->quality - $degradation);

        // Decrease sell-in
        $product->sell_in -= 1;
    }
}
