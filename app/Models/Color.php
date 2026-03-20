<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Color extends Model
{
    protected $guarded = [];

    protected $casts = [
        'additional_price' => 'decimal:2',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function materialType(): BelongsTo
    {
        return $this->belongsTo(MaterialType::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Color::class, 'parent_id');
    }

    public function subColors()
    {
        return $this->hasMany(Color::class, 'parent_id');
    }
}
