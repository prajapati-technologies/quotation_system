<?php

namespace App\Filament\Widgets;

use App\Models\Quotation;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentQuotationsWidget extends BaseWidget
{
    protected static ?int $sort = 3;
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $user = auth()->user();
        $isAdmin = $user->role === 'admin';

        $query = Quotation::query()->latest()->limit(5);

        if (!$isAdmin) {
            $query->whereHas('project.customer', fn($q) => $q->where('user_id', $user->id));
        }

        return $table
            ->heading('Recent Quotations')
            ->query($query)
            ->columns([
                Tables\Columns\TextColumn::make('project.name')
                    ->label('Project')
                    ->searchable()
                    ->weight('bold')
                    ->icon('heroicon-o-briefcase'),

                Tables\Columns\TextColumn::make('project.customer.name')
                    ->label('Customer')
                    ->searchable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Draft' => 'gray',
                        'Signed' => 'info',
                        'Approved' => 'success',
                        'Production' => 'warning',
                        'Completed' => 'success',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('final_price')
                    ->label('Amount')
                    ->formatStateUsing(fn($state) => '฿' . number_format($state, 2))
                    ->weight('semibold')
                    ->color('success'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M d, Y')
                    ->description(fn($record) => $record->created_at->diffForHumans()),
            ])
            ->actions([
                \Filament\Actions\Action::make('view')
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->url(fn(Quotation $record): string => route('filament.admin.resources.quotations.edit', $record)),
            ]);
    }
}
