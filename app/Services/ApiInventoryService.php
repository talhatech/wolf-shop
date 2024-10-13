<?php

namespace App\Services;

use App\Repositories\ProductRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Exception;
use App\Models\Product;
use Illuminate\Support\Str;
use App\Models\ProductRule;
use Illuminate\Support\Facades\Log;
use App\Enums\ProductCategoryEnum;
use Illuminate\Database\Eloquent\Collection;

class ApiInventoryService
{
    protected $apiEndpoint;

    protected $productRepository;

    // todo: there should be better way of this
    protected $categoryMapping = [
        'Apple AirPods' => ProductCategoryEnum::INCREASING_WITH_AGE,
        'Apple iPad Air' => ProductCategoryEnum::TIME_SENSITIVE,
        'Samsung Galaxy S23' => ProductCategoryEnum::LEGENDARY,
        'Xiaomi Redmi Note 13' => ProductCategoryEnum::CONJURED,
    ];

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
        $this->apiEndpoint = config('constants.products_mock_api.endpoint');
    }

    /**
     * Fetch data from the API and return the response.
     *
     * @return array|null
     */
    public function fetchInventoryData() :array|Exception
    {
        $response = Http::get($this->apiEndpoint);

        // todo: validate API response
        if ($response->successful()) {
            return $response->json(); // Return the JSON response as an array
        }
        // Log the error or handle it as needed
        throw new Exception('Failed to fetch data from the API: ' . $response->status());
    }

    // Handle the upsert logic
    public function processItem(array $item, Collection $productRules): void
    {
        try {
            // Map the product to the appropriate category or default to NORMAL
            $categoryName = $this->categoryMapping[$item['name']] ?? ProductCategoryEnum::NORMAL;

            // Fetch the corresponding product rule based on category
            $productRule = $productRules->where('category', $categoryName)->first();

            // Call the new upsert method in the ProductRepository
            $this->productRepository->upsertProduct($item, $productRule->id);
        } catch (Exception $e) {
            Log::error('Error processing item: ' . $e->getMessage());
        }
    }

    public function processBulkItem(array $items, Collection $productRules): void
    {
        foreach ($items as $item) {
            $this->processItem($item, $productRules);
        }
    }
}
