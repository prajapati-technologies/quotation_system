<?php

namespace App\Filament\Widgets;

use App\Models\Quotation;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;

class MonthlySalesReport extends Widget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';
    
    protected string $view = 'filament.widgets.monthly-sales-report';

    public ?string $salesPersonId = null;
    public ?string $startDate = null;
    public ?string $endDate = null;

    public function getData(): \Illuminate\Support\Collection
    {
        $actualSalesPersonId = $this->salesPersonId;
        if (empty($actualSalesPersonId) && auth()->user()->role === 'sales') {
            $actualSalesPersonId = auth()->id();
        }

        return \App\Models\Quotation::query()
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('SUM(final_price) as total_revenue')
            )
            ->where(function ($q) use ($actualSalesPersonId) {
                if ($actualSalesPersonId) {
                    $q->where(function($qq) use ($actualSalesPersonId) {
                        $qq->whereHas('project.customer', fn($q2) => $q2->where('user_id', $actualSalesPersonId))
                          ->orWhereHas('customer', fn($q2) => $q2->where('user_id', $actualSalesPersonId));
                    });
                }
            })
            ->when($this->startDate, fn($q) => $q->whereDate('created_at', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->whereDate('created_at', '<=', $this->endDate))
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->get();
    }
}
