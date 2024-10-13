<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use App\Services\ApiInventoryService;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Database\Eloquent\Collection;


class ProcessInventoryItemJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $items;

    protected $productRules;

    /**
     * Create a new job instance.
     */
    public function __construct(array $items, Collection $productRules)
    {
        $this->items = $items;
        $this->productRules = $productRules;
    }

    /**
     * Execute the job.
     */
    public function handle(ApiInventoryService $apiInventoryService): void
    {
        // Use the service to process the items
        $apiInventoryService->processBulkItem($this->items, $this->productRules);
    }
}
