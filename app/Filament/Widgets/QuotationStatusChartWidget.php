<?php

namespace App\Filament\Widgets;

use App\Models\Quotation;
use Filament\Widgets\ChartWidget;

class QuotationStatusChartWidget extends ChartWidget
{
    protected ?string $heading = 'Quotation Status Distribution';
    protected static ?int $sort = 4;
    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $user = auth()->user();
        $isAdmin = $user->role === 'admin';

        $query = Quotation::query();
        
        if (!$isAdmin) {
            $query->whereHas('project.customer', fn($q) => $q->where('user_id', $user->id));
        }

        $statuses = ['Draft', 'Signed', 'Approved', 'Production', 'Completed'];
        $data = [];
        
        foreach ($statuses as $status) {
            $data[] = (clone $query)->where('status', $status)->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Quotations',
                    'data' => $data,
                    'backgroundColor' => [
                        'rgba(107, 114, 128, 0.8)',  // Gray for Draft
                        'rgba(59, 130, 246, 0.8)',   // Blue for Signed
                        'rgba(34, 197, 94, 0.8)',    // Green for Approved
                        'rgba(251, 146, 60, 0.8)',   // Orange for Production
                        'rgba(155, 33, 33, 0.8)',    // Red for Completed
                    ],
                    'borderColor' => [
                        'rgb(107, 114, 128)',
                        'rgb(59, 130, 246)',
                        'rgb(34, 197, 94)',
                        'rgb(251, 146, 60)',
                        'rgb(155, 33, 33)',
                    ],
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $statuses,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
