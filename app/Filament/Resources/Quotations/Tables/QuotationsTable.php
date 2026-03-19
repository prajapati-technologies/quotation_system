<?php

namespace App\Filament\Resources\Quotations\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Support\Enums\FontWeight;

class QuotationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->actionsColumnLabel('Action')
            ->columns([
                TextColumn::make('quotation_number')
                    ->label('Quotation No')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold),

                TextColumn::make('project.name')
                    ->label('Project')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->description(fn($record) => $record->project->customer->name ?? 'N/A')
                    ->icon('heroicon-o-briefcase'),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Draft' => 'gray',
                        'Signed' => 'info',
                        'Approved' => 'success',
                        'Production' => 'warning',
                        'Completed' => 'success',
                        'Rejected' => 'danger',
                        default => 'gray',
                    })
                    ->icon(fn(string $state): string => match ($state) {
                        'Draft' => 'heroicon-o-document',
                        'Signed' => 'heroicon-o-pencil-square',
                        'Approved' => 'heroicon-o-check-circle',
                        'Production' => 'heroicon-o-wrench-screwdriver',
                        'Completed' => 'heroicon-o-flag',
                        'Rejected' => 'heroicon-o-x-circle',
                        default => 'heroicon-o-document',
                    })
                    ->searchable()
                    ->sortable(),

                TextColumn::make('total_price')
                    ->label('Total')
                    ->formatStateUsing(fn($state) => '฿' . number_format($state, 2))
                    ->sortable()
                    ->weight(FontWeight::SemiBold),



                TextColumn::make('final_price')
                    ->label('Final Price')
                    ->formatStateUsing(fn($state) => '฿' . number_format($state, 2))
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->color('success'),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->description(fn($record) => $record->created_at->diffForHumans())
                    ->toggleable(),


            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'Draft' => 'Draft',
                        'Approved' => 'Approved',
                        'Production' => 'Production',
                        'Completed' => 'Completed',
                        'Rejected' => 'Rejected',
                    ])
                    ->multiple(),
            ])
            ->recordActions([
                \Filament\Actions\ActionGroup::make([
                    ViewAction::make()
                        ->color('info')
                        ->modalWidth('7xl'),

                    EditAction::make()
                        ->visible(fn(\App\Models\Quotation $record) => auth()->user()->role === 'sales' && $record->status === 'Draft'),

                    Action::make('download')
                        ->label('PDF')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('gray')
                        ->action(fn(\App\Models\Quotation $record) => self::downloadPdf($record)),

                    Action::make('sign')
                        ->label('Sign')
                        ->icon('heroicon-o-pencil-square')
                        ->color('success')
                        ->visible(fn(\App\Models\Quotation $record) => $record->status === 'Draft' && auth()->user()->role === 'sales')
                        ->form([
                            \Filament\Forms\Components\ViewField::make('signature')
                                ->view('signature-modal')
                        ])
                        ->action(function (\App\Models\Quotation $record, array $data) {
                            if (isset($data['signature']) && !empty($data['signature'])) {
                                $image = $data['signature'];
                                $image = str_replace('data:image/png;base64,', '', $image);
                                $image = str_replace(' ', '+', $image);
                                $imageName = 'signatures/sign_' . $record->id . '.png';
                                \Illuminate\Support\Facades\Storage::disk('public')->put($imageName, base64_decode($image));

                                $record->update([
                                    'signature_path' => $imageName,
                                    'status' => 'Signed'
                                ]);
                            }
                        }),

                    Action::make('approve')
                        ->label('Approve')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->visible(fn(\App\Models\Quotation $record) => in_array($record->status, ['Draft', 'Signed']) && auth()->user()->role === 'admin')
                        ->action(fn(\App\Models\Quotation $record) => $record->update(['status' => 'Approved'])),

                    Action::make('production')
                        ->label('Production')
                        ->icon('heroicon-o-wrench-screwdriver')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->visible(fn(\App\Models\Quotation $record) => $record->status === 'Approved' && auth()->user()->role === 'admin')
                        ->action(fn(\App\Models\Quotation $record) => $record->update(['status' => 'Production'])),



                    Action::make('reject')
                        ->label('Reject')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->visible(fn(\App\Models\Quotation $record) => in_array($record->status, ['Draft', 'Signed', 'Approved']) && auth()->user()->role === 'admin')
                        ->action(fn(\App\Models\Quotation $record) => $record->update(['status' => 'Rejected'])),

                    Action::make('complete')
                        ->label('Complete')
                        ->icon('heroicon-o-flag')
                        ->color('success')
                        ->requiresConfirmation()
                        ->visible(fn(\App\Models\Quotation $record) => in_array($record->status, ['Approved', 'Production']) && auth()->user()->role === 'admin')
                        ->action(fn(\App\Models\Quotation $record) => $record->update(['status' => 'Completed'])),
                ])
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn() => auth()->user()->role === 'admin'),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }

    public static function downloadPdf(\App\Models\Quotation $quotation)
    {
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('quotation-pdf', ['quotation' => $quotation]);
        return response()->streamDownload(fn() => print ($pdf->output()), "Quotation-{$quotation->id}.pdf");
    }
}
