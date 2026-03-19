<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    protected $guarded = [];

    protected $casts = [
        'quotation_date' => 'date',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function items()
    {
        return $this->hasMany(QuotationItem::class);
    }

    /**
     * Virtual: Sum of all item-level installations
     */
    public function getInstallationTotalAttribute()
    {
        return $this->items->sum(function($item) {
            $area = ($item->width / 1000) * ($item->height / 1000);
            return $area * floatval($item->installation_cost) * intval($item->quantity);
        });
    }

    /**
     * Virtual: Sum of all item-level product prices (after discount)
     */
    public function getTotalGoodsAttribute()
    {
        return $this->items->sum('price');
    }
}
