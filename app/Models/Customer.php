<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function getCustomerNumberAttribute()
    {
        return 'CN' . str_pad($this->id, 3, '0', STR_PAD_LEFT);
    }
}
