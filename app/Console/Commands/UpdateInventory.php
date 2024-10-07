<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Services\WolfService;
use Illuminate\Console\Command;

class UpdateInventory extends Command
{
    private WolfService $wolfService;
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
    public function __construct(WolfService $wolfService)
    {
        parent::__construct();

        $this->wolfService = $wolfService;
    }

    public function handle()
    {
        $products = Product::with('rule')
        ->where('quality','>', 0)
        ->get();


        foreach ($products as $product) {
            // Todo: dispatch job with chunk
            $this->wolfService->updateProduct($product);
        }

        $this->info('Inventory updated successfully.');
    }
}
