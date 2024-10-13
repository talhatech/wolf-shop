<?php

namespace App\Console\Commands;

use App\Jobs\UpdateInventoryItemJob;
use App\Models\Product;
use App\Services\WolfService;
use Illuminate\Console\Command;

class UpdateInventory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-inventory';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update inventory items daily';

    /**
    * Execute the console command.
    */

    public function handle()
    {
        // Use cursor to stream products and process them in chunks
        Product::with('rule')
            ->where('quality', '>', 0)
            ->chunk(100, function ($products) { // can increase the chunk size.
                    // Dispatch job for each chunks of products
                    UpdateInventoryItemJob::dispatch($products);
            });

        $this->info('Inventory update has been queued successfully.');
    }
}
