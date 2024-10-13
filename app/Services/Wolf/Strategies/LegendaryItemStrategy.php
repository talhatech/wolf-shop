<?php declare(strict_types=1);

namespace App\Services\Wolf\Strategies;

use App\Models\Product;

class LegendaryItemStrategy implements ItemStrategy
{
    public function update(Product $product): void
    {
        // Legendary items do not change in quality or sell-in
    }
}
