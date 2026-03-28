<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MilestoneRequestResource\Pages;
use App\Models\Quotation;
use Filament\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MilestoneRequestResource extends Resource
{
    protected static ?string $model = Quotation::class;
    
    protected static ?string $slug = 'milestone-requests';

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-document-text';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Operations';
    }

    public static function getNavigationSort(): ?int
    {
        return 11;
    }

    public static function getNavigationLabel(): string
    {
        return 'Review Milestone Requests';
    }

    public static function getModelLabel(): string
    {
        return 'Milestone Request';
    }

    public static function canAccess(): bool
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getEloquentQuery()->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->whereNotNull('custom_milestone_request');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('formatted_reference')
                    ->label('Ref (CN / QT)')
                    ->searchable(['quotation_number'])
                    ->sortable(),
                TextColumn::make('project.name')
                    ->label('Project')
                    ->searchable(),
                TextColumn::make('customer.user.name')
                    ->label('Sales Person')
                    ->state(fn ($record) => $record->project?->customer?->user?->name ?? $record->customer?->user?->name ?? 'N/A'),
                TextColumn::make('final_price')
                    ->label('Final Price')
                    ->formatStateUsing(fn ($state) => '฿' . number_format($state, 2))
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label('Requested At')
                    ->dateTime()
                    ->sortable()
            ])
            ->actions([
                \Filament\Tables\Actions\Action::make('reviewCustomMilestones')
                    ->label('Review Request')
                    ->icon('heroicon-o-adjustments-horizontal')
                    ->color('warning')
                    ->button()
                    ->form(fn (Quotation $record) => [
                        Placeholder::make('info')
                            ->label('')
                            ->content('Sales requested the following milestone breakdown for this quotation:'),
                        Repeater::make('requested_milestones')
                            ->label('')
                            ->schema([
                                TextInput::make('label')->disabled(),
                                TextInput::make('percentage')->disabled()->suffix('%'),
                            ])
                            ->columns(2)
                            ->default($record->custom_milestone_request)
                            ->addable(false)
                            ->deletable(false)
                            ->reorderable(false),
                        Select::make('admin_action')
                            ->label('Decision')
                            ->options([
                                'approve' => 'Approve & Apply Request',
                                'reject' => 'Reject Request'
                            ])
                            ->required()
                    ])
                    ->action(function (Quotation $record, array $data) {
                        if ($data['admin_action'] === 'approve') {
                            // Delete existing milestones
                            $record->milestones()->delete();
                            
                            // Insert new milestones
                            $finalPrice = floatval($record->final_price);
                            foreach ($record->custom_milestone_request as $req) {
                                $record->milestones()->create([
                                    'label' => $req['label'],
                                    'percentage' => $req['percentage'],
                                    'amount' => round($finalPrice * (floatval($req['percentage']) / 100), 2),
                                    'status' => 'Pending'
                                ]);
                            }

                            $statusStr = 'Approved and Applied';
                            $icon = 'heroicon-o-check-circle';
                            $color = 'success';
                        } else {
                            $statusStr = 'Rejected';
                            $icon = 'heroicon-o-x-circle';
                            $color = 'danger';
                        }

                        $record->update(['custom_milestone_request' => null]);

                        $salesUser = $record->project?->customer?->user ?? $record->customer?->user;
                        if ($salesUser) {
                            Notification::make()
                                ->title('Milestone Request ' . $statusStr)
                                ->body("Your custom milestone request for {$record->formatted_reference} was {$statusStr}.")
                                ->icon($icon)
                                ->color($color)
                                ->sendToDatabase($salesUser)
                                ->send();
                        }

                        Notification::make()
                            ->title("Request {$statusStr}")
                            ->success()
                            ->send();
                    }),
            ])
            ->emptyStateHeading('No pending requests')
            ->emptyStateDescription('There are no custom milestone requests at the moment.');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMilestoneRequests::route('/'),
        ];
    }
}
