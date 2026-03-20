<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    protected $guarded = [];

    public function brandRates(): HasMany
    {
        return $this->hasMany(BrandRate::class);
    }

    public function colorPrices(): HasMany
    {
        return $this->hasMany(ProductColorPrice::class);
    }

    public function materialType(): BelongsTo
    {
        return $this->belongsTo(MaterialType::class);
    }
}
