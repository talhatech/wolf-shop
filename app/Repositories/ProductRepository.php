<?php declare(strict_types=1);

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Support\Str;


class ProductRepository extends BaseRepository
{
    public function __construct(Product $product)
    {
        parent::__construct($product);
    }

    // You can add additional methods specific to Product
    public function findByCategory(string $category): iterable
    {
        return $this->model->where('category', $category)->get();
    }

    public function findExpiringSoon(int $days): iterable
    {
        return $this->model->where('sell_in', '<=', $days)->get();
    }

    public function upsertProduct(array $item, $productRuleId): void
    {
        $this->model->upsert(
            [
                [
                    'id' => Str::uuid(),
                    'name' => $item['name'],
                    'details' => json_encode($item['data']),
                    'quality' => $item['quality'] ?? 12,
                    'sell_in' => $item['sellIn'] ?? 12,
                    'product_rule_id' => $productRuleId,
                    'external_id' => $item['id'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            ],
            ['external_id'],
            ['quality', 'sell_in', 'product_rule_id', 'details', 'updated_at']
        );
    }
}
