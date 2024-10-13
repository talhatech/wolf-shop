<?php

namespace App\Console\Commands;

use Exception;
use App\Models\ProductRule;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Services\ApiInventoryService;
use App\Jobs\ProcessInventoryItemJob;

class ImportInventory extends Command
{
    private $apiInventoryService;

    protected $signature = 'app:import-inventory';

    protected $description = 'Import inventory items/products from API and update the database';

    public function __construct(ApiInventoryService $apiInventoryService)
    {
        parent::__construct();
        $this->apiInventoryService = $apiInventoryService;
    }

    // Execute the console command
    public function handle(): void
    {
        try {
                $productRules = ProductRule::all();

                $items = $this->apiInventoryService->fetchInventoryData();

                // Dispatch jobs for each batch of items
                foreach (array_chunk($items, 30) as $itemBatch) {
                        ProcessInventoryItemJob::dispatch($itemBatch, $productRules);
                }

                $this->info('Inventory import has been queued successfully.');

            } catch (Exception $e) {
                Log::error('Error during inventory import: ' . $e->getMessage());
                $this->error('Failed to import inventory: ' . $e->getMessage());
            }
    }
}
