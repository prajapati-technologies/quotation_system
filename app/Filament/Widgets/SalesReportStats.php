<?php

namespace App\Filament\Widgets;

use App\Models\Quotation;
use App\Models\QuotationMilestone;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SalesReportStats extends BaseWidget
{
    public ?string $salesPersonId = null;
    public ?string $startDate = null;
    public ?string $endDate = null;

    protected function getStats(): array
    {
        $actualSalesPersonId = $this->salesPersonId;
        
        // If on the dashboard and user is sales, filter by their ID
        if (empty($actualSalesPersonId) && auth()->user()->role === 'sales') {
            $actualSalesPersonId = auth()->id();
        }

        $query = Quotation::query()
            ->when($actualSalesPersonId, function($q) use ($actualSalesPersonId) {
                $q->where(function($qq) use ($actualSalesPersonId) {
                    $qq->whereHas('project.customer', fn($q2) => $q2->where('user_id', $actualSalesPersonId))
                      ->orWhereHas('customer', fn($q2) => $q2->where('user_id', $actualSalesPersonId));
                });
            })
            ->when($this->startDate, fn($q) => $q->whereDate('created_at', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->whereDate('created_at', '<=', $this->endDate));

        $totalSales = (float) $query->sum('final_price');
        $quotationIds = $query->pluck('id');
        
        $totalReceived = (float) QuotationMilestone::whereIn('quotation_id', $quotationIds)
            ->where('status', 'Approved')
            ->sum('amount');
            
        $totalPending = max(0, $totalSales - $totalReceived);

        return [
            Stat::make('Gross Sales (THB)', '฿' . number_format($totalSales, 2))
                ->description('Total project value')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('info'),
            Stat::make('Payments Collected', '฿' . number_format($totalReceived, 2))
                ->description('Amount verified by Admin')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),
            Stat::make('Outstanding Due', '฿' . number_format($totalPending, 2))
                ->description('Remaining pending amount')
                ->descriptionIcon('heroicon-m-clock')
                ->color('danger'),
        ];
    }
}
