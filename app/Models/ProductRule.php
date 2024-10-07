<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductRule extends BaseModel
{
    protected $fillable = ['max_quality', 'min_quality', 'daily_decrease', 'sell_in_threshold'];

    public function products(): HasMany
    {
        return $this->hasMany(ProductRule::class, 'product_rule_id');
    }
}
