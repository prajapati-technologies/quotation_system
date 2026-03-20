<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProductColorPrice extends Model
{
    protected $guarded = [];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function mainColor(): BelongsTo
    {
        return $this->belongsTo(Color::class, 'main_color_id');
    }

    public function subColors(): BelongsToMany
    {
        return $this->belongsToMany(Color::class, 'product_color_price_sub_color', 'product_color_price_id', 'color_id');
    }
}
