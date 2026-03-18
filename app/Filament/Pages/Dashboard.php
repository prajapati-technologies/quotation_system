<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    public function getWidgets(): array
    {
        return [
            \App\Filament\Widgets\StatsOverviewWidget::class,
            \App\Filament\Widgets\RevenueChartWidget::class,
            \App\Filament\Widgets\QuotationStatusChartWidget::class,
            \App\Filament\Widgets\RecentQuotationsWidget::class,
        ];
    }

    public function getColumns(): int | array
    {
        return 2;
    }
}
