<?php

namespace App\Filament\Resources;

use App\Filament\Resources\Quotations\QuotationResource;
use App\Filament\Resources\QuotationMilestoneResource\Pages;
use App\Models\QuotationMilestone;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Actions\Action;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class QuotationMilestoneResource extends Resource
{
    protected static ?string $model = QuotationMilestone::class;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-banknotes';
    }

    public static function getNavigationSort(): ?int
    {
        return 3;
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Operations';
    }

    public static function getNavigationLabel(): string
    {
        return 'Milestone Payments';
    }

    public static function canAccess(): bool
    {
        return auth()->check();
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->whereNotNull('receipt_path');

        if (auth()->check() && auth()->user()->role === 'sales') {
            $query->where(function (Builder $q) {
                $q->whereHas('quotation.project.customer', function (Builder $q) {
                    $q->where('user_id', auth()->id());
                })->orWhereHas('quotation.customer', function (Builder $q) {
                    $q->where('user_id', auth()->id());
                });
            });
        }
        return $query;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('quotation.formatted_reference')
                    ->label('Reference')
                    ->searchable()
                    ->url(fn($record) => QuotationResource::getUrl('edit', ['record' => $record->quotation_id])),
                
                TextColumn::make('quotation.project.customer.name')
                    ->label('Customer')
                    ->searchable(),
                
                TextColumn::make('sales_person')
                    ->label('Sales Person')
                    ->state(fn($record) => $record->quotation?->project?->customer?->user?->name ?? $record->quotation?->customer?->user?->name ?? 'N/A')
                    ->searchable()
                    ->visible(fn() => auth()->user()->role === 'admin'),

                TextColumn::make('label')
                    ->label('Milestone')
                    ->description(fn($record) => $record->percentage . '% of total')
                    ->searchable(),

                TextColumn::make('amount')
                    ->label('Amount')
                    ->money('THB')
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Pending' => 'gray',
                        'Paid' => 'warning',
                        'Approved' => 'success',
                        'Rejected' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('paid_at')
                    ->label('Paid On')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('receipt_path')
                    ->label('Receipt')
                    ->formatStateUsing(fn ($state) => $state ? 'View Receipt' : 'No Receipt')
                    ->url(fn ($state) => $state ? asset('storage/' . $state) : null)
                    ->openUrlInNewTab()
                    ->color(fn ($state) => $state ? 'primary' : 'gray'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'Paid' => 'Paid (Waiting Approval)',
                        'Approved' => 'Approved',
                        'Rejected' => 'Rejected',
                    ]),
                SelectFilter::make('sales_person')
                    ->label('Sales Person')
                    ->options(\App\Models\User::where('role', 'sales')->pluck('name', 'id'))
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['value'])) {
                            $query->where(function ($q) use ($data) {
                                $q->whereHas('quotation.project.customer', function ($q) use ($data) {
                                    $q->where('user_id', $data['value']);
                                })->orWhereHas('quotation.customer', function ($q) use ($data) {
                                    $q->where('user_id', $data['value']);
                                });
                            });
                        }
                    })
                    ->visible(fn () => auth()->user()->role === 'admin'),
            ])
            ->actions([
                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn () => auth()->user()->role === 'admin')
                    ->hidden(fn ($record) => $record->status === 'Approved')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update([
                            'status' => 'Approved',
                            'approved_at' => now(),
                            'approved_by' => auth()->id(),
                        ]);

                        // Notify Sales Person
                        $salesUser = $record->quotation?->project?->customer?->user ?? $record->quotation?->customer?->user;
                        if ($salesUser) {
                            Notification::make()
                                ->title('Payment Approved')
                                ->body("Payment for milestone '{$record->label}' has been approved.")
                                ->success()
                                ->sendToDatabase($salesUser)
                                ->send(); // toast
                        }
                    }),

                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn () => auth()->user()->role === 'admin')
                    ->hidden(fn ($record) => $record->status === 'Approved')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update([
                            'status' => 'Rejected',
                            'approved_at' => null,
                            'approved_by' => null,
                        ]);

                        // Notify Sales Person
                        $salesUser = $record->quotation?->project?->customer?->user ?? $record->quotation?->customer?->user;
                        if ($salesUser) {
                            Notification::make()
                                ->title('Payment Rejected')
                                ->body("Payment for milestone '{$record->label}' has been rejected.")
                                ->danger()
                                ->sendToDatabase($salesUser)
                                ->send(); // toast
                        }
                    }),
                Action::make('download_receipt_milestone')
                    ->label('Download PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('info')
                    ->visible(fn ($record) => $record->status === 'Approved')
                    ->action(fn ($record) => \App\Filament\Resources\Quotations\Tables\QuotationsTable::downloadPdf($record->quotation, 'receipt_milestone', $record->id)),
            ])
            ->defaultSort('paid_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQuotationMilestones::route('/'),
        ];
    }
}
