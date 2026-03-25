<?php

namespace App\Filament\Resources\Quotations\Tables;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class QuotationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->actionsColumnLabel('Action')
            ->columns([
                TextColumn::make('id')
                    ->label('Ref (CN / QT)')
                    ->formatStateUsing(fn (\App\Models\Quotation $record): string => $record->formatted_reference)
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold),

                TextColumn::make('project.name')
                    ->label('Project')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->description(fn ($record) => $record->project->customer->name ?? 'N/A')
                    ->icon('heroicon-o-briefcase'),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Draft' => 'gray',
                        'Signed' => 'info',
                        'Approved' => 'success',
                        'Production' => 'warning',
                        'Completed' => 'success',
                        'Rejected' => 'danger',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
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
                    ->formatStateUsing(fn ($state) => '฿'.number_format($state, 2))
                    ->sortable()
                    ->weight(FontWeight::SemiBold),

                TextColumn::make('final_price')
                    ->label('Final Price')
                    ->formatStateUsing(fn ($state) => '฿'.number_format($state, 2))
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->color('success'),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->description(fn ($record) => $record->created_at->diffForHumans())
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
                ActionGroup::make([
                    ViewAction::make()
                        ->color('info')
                        ->modalWidth('7xl'),

                    EditAction::make()
                        ->visible(fn (\App\Models\Quotation $record) => auth()->user()->role === 'sales' && $record->status === 'Draft'),

                    ActionGroup::make([
                        Action::make('downloadQuotationPdf')
                            ->label('Quotation')
                            ->icon('heroicon-o-document-text')
                            ->action(fn (\App\Models\Quotation $record) => self::downloadPdf($record, 'quotation')),
                        Action::make('downloadInvoicePdf')
                            ->label('Invoice')
                            ->icon('heroicon-o-document-currency-dollar')
                            ->action(fn (\App\Models\Quotation $record) => self::downloadPdf($record, 'invoice')),
                        Action::make('pdfPartialPaymentReceipt')
                            ->label('Partial payment receipt')
                            ->icon('heroicon-o-receipt-percent')
                            ->modalHeading('Partial payment receipt')
                            ->modalDescription('Invoice total (VAT sahit) ka jitna bhi % aapne liya ho yahan likho — 40, 50, 80, 90, kuch bhi 0.01% se 99.99% tak. Save ke baad partial receipt PDF download hogi.')
                            ->modalHidden(fn (\App\Models\Quotation $record): bool => $record->partial_payment_at !== null
                                || ! self::canRecordPaymentsForQuotation($record)
                                || $record->full_payment_at !== null)
                            ->form(fn (\App\Models\Quotation $record): array => $record->partial_payment_at === null
                                && self::canRecordPaymentsForQuotation($record)
                                && $record->full_payment_at === null
                                ? [
                                    TextInput::make('payment_percent')
                                        ->label('Payment percentage')
                                        ->numeric()
                                        ->minValue(0.01)
                                        ->maxValue(99.99)
                                        ->suffix('%')
                                        ->required()
                                        ->placeholder('e.g. 40, 50, 80, 90')
                                        ->helperText('Koi bhi percentage chalega (final amount ka hissa). Examples: 40, 50, 80, 90 — minimum 0.01%, maximum 99.99% (taake baaki amount baad mein full payment receipt se ho).'),
                                ]
                                : [])
                            ->action(function (\App\Models\Quotation $record, array $data) {
                                if ($record->partial_payment_at !== null) {
                                    return self::downloadPdf($record, 'receipt_partial');
                                }

                                if ($record->full_payment_at !== null) {
                                    Notification::make()
                                        ->title('No partial payment receipt')
                                        ->body('This invoice was paid in full without a recorded partial payment, so there is no partial receipt to download.')
                                        ->warning()
                                        ->send();

                                    return;
                                }

                                if (! self::canRecordPaymentsForQuotation($record)) {
                                    Notification::make()
                                        ->title('Not available yet')
                                        ->body('Approve this quotation first (status: Approved, Production, or Completed). Then you can record a partial payment and download the receipt.')
                                        ->warning()
                                        ->send();

                                    return;
                                }

                                $pct = (float) ($data['payment_percent'] ?? 0);
                                if ($pct < 0.01 || $pct > 99.99) {
                                    Notification::make()
                                        ->title('Invalid percentage')
                                        ->body('Percentage 0.01% se 99.99% ke beech honi chahiye (jaise 80 ya 90).')
                                        ->danger()
                                        ->send();

                                    return;
                                }

                                $final = (float) $record->final_price;
                                $amount = round($final * ($pct / 100), 2);
                                $record->update([
                                    'partial_payment_percent' => $pct,
                                    'partial_payment_amount' => $amount,
                                    'partial_payment_at' => now(),
                                ]);

                                return self::downloadPdf($record->fresh(), 'receipt_partial');
                            })
                            ->successNotificationTitle(null),
                        Action::make('pdfFullPaymentReceipt')
                            ->label('Full payment receipt')
                            ->icon('heroicon-o-banknotes')
                            ->modalHeading('Record full payment')
                            ->modalDescription(fn (\App\Models\Quotation $record): string => $record->partial_payment_at !== null
                                ? 'This records the remaining balance and downloads the full payment receipt PDF.'
                                : 'This records payment for the entire invoice (no prior partial payment) and downloads the receipt PDF.')
                            ->modalHidden(fn (\App\Models\Quotation $record): bool => $record->full_payment_at !== null
                                || ! self::canRecordPaymentsForQuotation($record))
                            ->requiresConfirmation(fn (\App\Models\Quotation $record): bool => $record->full_payment_at === null
                                && self::canRecordPaymentsForQuotation($record))
                            ->action(function (\App\Models\Quotation $record) {
                                if ($record->full_payment_at !== null) {
                                    return self::downloadPdf($record, 'receipt_full');
                                }

                                if (! self::canRecordPaymentsForQuotation($record)) {
                                    Notification::make()
                                        ->title('Not available yet')
                                        ->body('Approve this quotation first (status: Approved, Production, or Completed). Then you can record full payment and download the receipt.')
                                        ->warning()
                                        ->send();

                                    return;
                                }

                                $final = (float) $record->final_price;
                                $partial = (float) ($record->partial_payment_amount ?? 0);
                                $balance = round(max(0, $final - $partial), 2);
                                $record->update([
                                    'full_payment_at' => now(),
                                    'full_payment_balance_amount' => $balance,
                                ]);

                                return self::downloadPdf($record->fresh(), 'receipt_full');
                            })
                            ->successNotificationTitle(null),
                    ])
                        ->label('PDF')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('gray')
                        ->button(),

                    Action::make('sign')
                        ->label('Sign')
                        ->icon('heroicon-o-pencil-square')
                        ->color('success')
                        ->visible(fn (\App\Models\Quotation $record) => $record->status === 'Draft' && auth()->user()->role === 'sales')
                        ->form([
                            \Filament\Forms\Components\ViewField::make('signature')
                                ->view('signature-modal'),
                        ])
                        ->action(function (\App\Models\Quotation $record, array $data) {
                            if (isset($data['signature']) && ! empty($data['signature'])) {
                                $image = $data['signature'];
                                $image = str_replace('data:image/png;base64,', '', $image);
                                $image = str_replace(' ', '+', $image);
                                $imageName = 'signatures/sign_'.$record->id.'.png';
                                \Illuminate\Support\Facades\Storage::disk('public')->put($imageName, base64_decode($image));

                                $record->update([
                                    'signature_path' => $imageName,
                                    'status' => 'Signed',
                                ]);
                            }
                        }),

                    Action::make('approve')
                        ->label('Approve')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->visible(fn (\App\Models\Quotation $record) => in_array($record->status, ['Draft', 'Signed']) && auth()->user()->role === 'admin')
                        ->action(fn (\App\Models\Quotation $record) => $record->update(['status' => 'Approved'])),

                    Action::make('production')
                        ->label('Production')
                        ->icon('heroicon-o-wrench-screwdriver')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->visible(fn (\App\Models\Quotation $record) => $record->status === 'Approved' && auth()->user()->role === 'admin')
                        ->action(fn (\App\Models\Quotation $record) => $record->update(['status' => 'Production'])),

                    Action::make('reject')
                        ->label('Reject')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->visible(fn (\App\Models\Quotation $record) => in_array($record->status, ['Draft', 'Signed', 'Approved']) && auth()->user()->role === 'admin')
                        ->action(fn (\App\Models\Quotation $record) => $record->update(['status' => 'Rejected'])),

                    Action::make('complete')
                        ->label('Complete')
                        ->icon('heroicon-o-flag')
                        ->color('success')
                        ->requiresConfirmation()
                        ->visible(fn (\App\Models\Quotation $record) => in_array($record->status, ['Approved', 'Production']) && auth()->user()->role === 'admin')
                        ->action(fn (\App\Models\Quotation $record) => $record->update(['status' => 'Completed'])),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()->role === 'admin'),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }

    public static function canRecordPaymentsForQuotation(\App\Models\Quotation $record): bool
    {
        return in_array($record->status, ['Approved', 'Production', 'Completed'], true);
    }

    public static function downloadPdf(\App\Models\Quotation $quotation, string $documentType = 'quotation'): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $documentType = in_array($documentType, ['quotation', 'invoice', 'receipt_partial', 'receipt_full'], true)
            ? $documentType
            : 'quotation';

        if ($documentType === 'receipt_partial' && $quotation->partial_payment_at === null) {
            abort(404, 'Partial payment has not been recorded for this quotation.');
        }

        if ($documentType === 'receipt_full' && $quotation->full_payment_at === null) {
            abort(404, 'Full payment has not been recorded for this quotation.');
        }

        $quotation->loadMissing([
            'customer',
            'project.customer',
            'items.product',
            'items.color',
            'items.glass',
        ]);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('quotation-pdf', [
            'quotation' => $quotation,
            'documentType' => $documentType,
        ]);

        $fileStem = match ($documentType) {
            'invoice' => 'Invoice-'.$quotation->invoice_number,
            'receipt_partial' => 'Receipt-'.$quotation->receipt_number_partial,
            'receipt_full' => 'Receipt-'.$quotation->receipt_number_full,
            default => 'Quotation-'.$quotation->quotation_number,
        };

        $safeName = preg_replace('/[^\w\-.]+/u', '_', $fileStem).'.pdf';

        return response()->streamDownload(fn () => print ($pdf->output()), $safeName);
    }
}
