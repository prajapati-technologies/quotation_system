<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    protected $guarded = [];

    protected $casts = [
        'quotation_date' => 'date',
        'total_goods' => 'decimal:2',
        'installation_total' => 'decimal:2',
        'total_price' => 'decimal:2',
        'discount' => 'decimal:2',
        'vat_percent' => 'decimal:2',
        'vat_total' => 'decimal:2',
        'final_price' => 'decimal:2',
        'partial_payment_percent' => 'decimal:2',
        'partial_payment_amount' => 'decimal:2',
        'partial_payment_at' => 'datetime',
        'full_payment_at' => 'datetime',
        'full_payment_balance_amount' => 'decimal:2',
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

    public function milestones()
    {
        return $this->hasMany(QuotationMilestone::class);
    }

    public function getQuotationNumberAttribute()
    {
        return 'QT'.str_pad($this->id, 4, '0', STR_PAD_LEFT);
    }

    /** Invoice PDF number (paired with quotation id). */
    public function getInvoiceNumberAttribute(): string
    {
        return 'INV'.str_pad((string) $this->id, 4, '0', STR_PAD_LEFT);
    }

    /** Receipt PDF number (legacy single receipt label). */
    public function getReceiptNumberAttribute(): string
    {
        return 'RCP'.str_pad((string) $this->id, 4, '0', STR_PAD_LEFT);
    }

    public function getReceiptNumberPartialAttribute(): string
    {
        return $this->receipt_number.'-P';
    }

    public function getReceiptNumberFullAttribute(): string
    {
        return $this->receipt_number.'-F';
    }

    /**
     * Customer no + quotation no for documents (e.g. #CN001 / #QT0021).
     */
    public function getFormattedReferenceAttribute(): string
    {
        $cn = $this->customer?->customer_number ?? $this->project?->customer?->customer_number;
        $qt = $this->quotation_number;

        return $cn ? "#{$cn} / #{$qt}" : "#{$qt}";
    }
}
