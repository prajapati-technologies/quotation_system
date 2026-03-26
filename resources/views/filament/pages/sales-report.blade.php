<x-filament-panels::page>
    <div class="mb-4">
        {{ $this->form }}
    </div>

    <div class="space-y-6">
        @livewire(\App\Filament\Widgets\SalesReportStats::class, [
            'salesPersonId' => $salesPersonId,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ], key('stats-'.($salesPersonId ?? 'all').'-'.$startDate.'-'.$endDate))

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @livewire(\App\Filament\Widgets\MonthlySalesReport::class, [
                'salesPersonId' => $salesPersonId,
                'startDate' => $startDate,
                'endDate' => $endDate,
            ], key('monthly-'.($salesPersonId ?? 'all').'-'.$startDate.'-'.$endDate))

            @livewire(\App\Filament\Widgets\SalesLeaderboard::class, [
                'salesPersonId' => $salesPersonId,
                'startDate' => $startDate,
                'endDate' => $endDate,
            ], key('leaderboard-'.($salesPersonId ?? 'all').'-'.$startDate.'-'.$endDate))
        </div>
    </div>
</x-filament-panels::page>
