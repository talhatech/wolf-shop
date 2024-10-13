<?php declare(strict_types=1);

namespace App\Services\Wolf\Strategies;

use App\Models\Product;

interface ItemStrategy
{
    public function update(Product $product): void;
}
