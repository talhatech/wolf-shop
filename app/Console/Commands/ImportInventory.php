<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use App\Services\ApiInventoryService;
use App\Enums\ProductCategoryEnum;
use App\Models\{Product, ProductRule};

class ImportInventory extends Command
{
    private $productRules;

    private $apiInventoryService;

    protected $signature = 'app:import-inventory';

    protected $description = 'Import inventory items/products from API and update the database';

    // todo: there should be better way of this
    protected $categoryMapping = [
            'Apple AirPods' => ProductCategoryEnum::INCREASING_WITH_AGE,
            'Apple iPad Air' => ProductCategoryEnum::TIME_SENSITIVE,
            'Samsung Galaxy S23' => ProductCategoryEnum::LEGENDARY,
            'Xiaomi Redmi Note 13' => ProductCategoryEnum::CONJURED,
        ];

    public function __construct(ApiInventoryService $apiInventoryService)
    {
        parent::__construct();

        $this->apiInventoryService = $apiInventoryService;
        $this->productRules = ProductRule::all();
    }

    // Execute the console command
    public function handle(): void
    {
        try {
            $items = $this->apiInventoryService->fetchInventoryData();

            foreach ($items as $item) { $this->upsertItem($item); }

            $this->info('Inventory import completed successfully.');

        } catch (Exception $e) {
            // Log the error message and show it in the console output
            \Log::error('Error during inventory import: ' . $e->getMessage());

            // Output error to console
            $this->error('Failed to import inventory: ' . $e->getMessage());
        }
    }

    private function upsertItem($itemData): void
    {
        $categoryName = $this->categoryMapping[$itemData['name']] ?? ProductCategoryEnum::NORMAL;
        $productRule = $this->productRules->where('category', $categoryName)->first();

        // todo: move default quality and sellIn to config
        $affectedRows = Product::upsert(
            [
                [
                    'id' => Str::uuid(), // Generate a UUID for the new product
                    'name' => $itemData['name'],
                    'details' => json_encode($itemData['data']),
                    'quality' => $itemData['quality'] ?? 12,
                    'sell_in' => $itemData['sellIn'] ?? 12,
                    'product_rule_id' => $productRule->id,
                    'external_id' => $itemData['id'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            ],
         ['external_id'], ['quality', 'sell_in', 'product_rule_id', 'details', 'updated_at']);

        if ($affectedRows > 0) {
            $this->info("Processed product: {$itemData['name']}. Rows affected: {$affectedRows}");
        }
    }
}
