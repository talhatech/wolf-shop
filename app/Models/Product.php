<?php

namespace App\Models;

use App\Models\BaseClass\Item;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends BaseModel
{
    private Item $item;

    protected $fillable = [
        'id',
        'name',
        'details',
        'sell_in',
        'quality',
        'external_id', // restful-api id
        'product_rule_id'
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->item = new Item(
            $attributes['name'] ?? '',
            $attributes['sell_in'] ?? 0,
            $attributes['quality'] ?? 0
        );
    }

    // Access the product rules
    public function rule(): BelongsTo
    {
        return $this->belongsTo(ProductRule::class, 'product_rule_id');
    }

    /**
     * Get the decoded details as an array.
     *
     * @return array|null
     */
    public function getDetailsAttribute(): ?array
    {
        return json_decode($this->attributes['details'], true);
    }

    // Getter to access the underlying Item instance
    public function getItem(): Item
    {
        return $this->item;
    }

    // Proxy methods to interact with Item (if needed)
    public function __toString(): string
    {
        return $this->item->__toString();  // Delegate to Item's __toString
    }

    public function getImgUrl(): string
    {
        return $this->item->getImgUrl();  // Delegate to Item's getImgUrl
    }

    public function setImgUrl(string $imgUrl): self
    {
        $this->item->setImgUrl($imgUrl);
        return $this;
    }
}
