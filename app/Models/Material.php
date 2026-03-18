<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Material extends Model
{
    protected $guarded = [];

    public function materialTypes(): HasMany
    {
        return $this->hasMany(MaterialType::class);
    }

    public function brands(): HasMany
    {
        return $this->hasMany(Brand::class);
    }
}
