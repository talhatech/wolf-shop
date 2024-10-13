<?php

namespace App\Services\Wolf;

use App\Models\Product;
use App\Repositories\ProductRepository;
use App\Services\Wolf\Factories\ItemStrategyFactory;


class WolfService
{
    private ProductRepository $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function updateProduct(Product $product): void
    {
        // Get the strategy for this product type
        $strategy = ItemStrategyFactory::create($product);


        $strategy->update($product);

        // Persist the updated product data
        $this->productRepository->save($product);
    }
}
