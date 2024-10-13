<?php

namespace App\Jobs;

use App\Services\WolfService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Database\Eloquent\Collection;


class UpdateInventoryItemJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $products;

    protected $wolfService;

    /**
     * Create a new job instance.
     */
    public function __construct(Collection $products)
    {
        $this->products = $products;
        $this->wolfService = new WolfService();
    }

    /**im
     * Execute the job.
     */
    public function handle(): void
    {
        foreach ($this->products as $product) {
            $this->wolfService->updateProduct($product);
        }
    }
}
