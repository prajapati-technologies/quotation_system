<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MaterialType extends Model
{
    protected $guarded = [];

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    public function colors(): HasMany
    {
        return $this->hasMany(Color::class);
    }

    public function mainColors(): HasMany
    {
        return $this->hasMany(Color::class)->whereNull('parent_id');
    }
}
