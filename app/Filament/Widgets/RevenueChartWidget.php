<?php

namespace App\Filament\Widgets;

use App\Models\Quotation;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class RevenueChartWidget extends ChartWidget
{
    protected ?string $heading = 'Revenue Trend (Last 6 Months)';
    protected static ?int $sort = 2;
    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $user = auth()->user();
        $isAdmin = $user->role === 'admin';

        // Get last 6 months
        $months = collect();
        for ($i = 5; $i >= 0; $i--) {
            $months->push(Carbon::now()->subMonths($i));
        }

        $data = $months->map(function ($month) use ($isAdmin, $user) {
            $query = Quotation::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->whereIn('status', ['Approved', 'Production', 'Completed']);

            if (!$isAdmin) {
                $query->whereHas('project.customer', fn($q) => $q->where('user_id', $user->id));
            }

            return $query->sum('final_price');
        });

        return [
            'datasets' => [
                [
                    'label' => 'Revenue ($)',
                    'data' => $data->toArray(),
                    'backgroundColor' => 'rgba(99, 102, 241, 0.1)', // Indigo-500 with opacity
                    'borderColor' => '#6366f1', // Indigo-500
                    'borderWidth' => 2,
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $months->map(fn($m) => $m->format('M Y'))->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
