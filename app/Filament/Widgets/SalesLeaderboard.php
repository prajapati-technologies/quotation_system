<?php

namespace App\Filament\Widgets;

use App\Models\Quotation;
use App\Models\QuotationMilestone;
use App\Models\User;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class SalesLeaderboard extends BaseWidget
{
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';
    
    public ?string $salesPersonId = null;
    public ?string $startDate = null;
    public ?string $endDate = null;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                User::where('role', 'sales')
                    ->when($this->salesPersonId, fn($q) => $q->where('id', $this->salesPersonId))
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Sales Professional')
                    ->weight('bold')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_sales')
                    ->label('Total Project Value (THB)')
                    ->getStateUsing(function ($record) {
                        return (float) \App\Models\Quotation::query()
                            ->where(function ($q) use ($record) {
                                $q->whereHas('project.customer', fn($q2) => $q2->where('user_id', $record->id))
                                  ->orWhereHas('customer', fn($q2) => $q2->where('user_id', $record->id));
                            })
                            ->when($this->startDate, fn($q) => $q->whereDate('created_at', '>=', $this->startDate))
                            ->when($this->endDate, fn($q) => $q->whereDate('created_at', '<=', $this->endDate))
                            ->sum('final_price');
                    })
                    ->formatStateUsing(fn ($state) => '฿' . number_format($state, 2))
                    ->alignRight()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('collected')
                    ->label('Collection %')
                    ->getStateUsing(function ($record) {
                        $total = (float) \App\Models\Quotation::query()
                            ->where(function ($q) use ($record) {
                                $q->whereHas('project.customer', fn($q2) => $q2->where('user_id', $record->id))
                                  ->orWhereHas('customer', fn($q2) => $q2->where('user_id', $record->id));
                            })
                            ->when($this->startDate, fn($q) => $q->whereDate('created_at', '>=', $this->startDate))
                            ->when($this->endDate, fn($q) => $q->whereDate('created_at', '<=', $this->endDate))
                            ->sum('final_price');
                        
                        if ($total == 0) return '0.0%';
                        
                        $received = \App\Models\QuotationMilestone::query()
                            ->whereHas('quotation', function($q) use ($record) {
                                $q->where(function ($qq) use ($record) {
                                    $qq->whereHas('project.customer', fn($q2) => $q2->where('user_id', $record->id))
                                      ->orWhereHas('customer', fn($q2) => $q2->where('user_id', $record->id));
                                })
                                ->when($this->startDate, fn($q) => $q->whereDate('created_at', '>=', $this->startDate))
                                ->when($this->endDate, fn($q) => $q->whereDate('created_at', '<=', $this->endDate));
                            })
                            ->where('status', 'Approved')
                            ->sum('amount');
                                
                        return number_format(($received / $total) * 100, 1) . '%';
                    })
                    ->badge()
                    ->color('success')
                    ->alignCenter(),
            ])
            ->paginated(false);
    }
}
