<?php

namespace App\Filament\Resources\Quotations\Tables;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Placeholder;
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
                    ->formatStateUsing(function (string $state, \App\Models\Quotation $record): string {
                        if (auth()->user()->role === 'sales' && $record->milestones()->where('status', 'Paid')->exists()) {
                            return 'Waiting for admin approval';
                        }
                        return $state;
                    })
                    ->color(fn (string $state, $record): string => match ($state === 'Approved' && auth()->user()->role === 'sales' && $record->milestones()->where('status', 'Paid')->exists() ? 'WaitAdmin' : $state) {
                        'Draft' => 'gray',
                        'WaitAdmin' => 'warning',
                        'Signed' => 'info',
                        'Approved' => 'success',
                        'Production' => 'warning',
                        'Completed' => 'success',
                        'Rejected' => 'danger',
                        default => 'gray',
                    })
                    ->icon(fn (string $state, $record): string => match ($state === 'Approved' && auth()->user()->role === 'sales' && $record->milestones()->where('status', 'Paid')->exists() ? 'WaitAdmin' : $state) {
                        'Draft' => 'heroicon-o-document',
                        'WaitAdmin' => 'heroicon-o-clock',
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
                    
                TextColumn::make('milestone_request_status')
                    ->label('Custom Milestone')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'Pending' => 'warning',
                        'Approved' => 'success',
                        'Rejected' => 'danger',
                        default => 'gray',
                    })
                    ->visible(fn () => auth()->user()->role === 'sales')
                    ->searchable()
                    ->sortable()
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
                SelectFilter::make('sales_person')
                    ->label('Filter By Sales Person')
                    ->options(\App\Models\User::where('role', 'sales')->pluck('name', 'id'))
                    ->query(function ($query, array $data) {
                        if (!empty($data['value'])) {
                            $query->where(function ($q) use ($data) {
                                $q->whereHas('project.customer', function ($q) use ($data) {
                                    $q->where('user_id', $data['value']);
                                })->orWhereHas('customer', function ($q) use ($data) {
                                    $q->where('user_id', $data['value']);
                                });
                            });
                        }
                    })
                    ->visible(fn () => auth()->user()->role === 'admin'),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->color('info')
                        ->modalWidth('7xl'),

                    EditAction::make()
                        ->visible(fn (\App\Models\Quotation $record) => auth()->user()->role === 'admin' || (auth()->user()->role === 'sales' && in_array($record->status, ['Draft', 'Approved']))),

                    ActionGroup::make([
                        Action::make('downloadQuotationPdf')
                            ->label('Quotation')
                            ->icon('heroicon-o-document-text')
                            ->action(fn (\App\Models\Quotation $record) => self::downloadPdf($record, 'quotation')),
                        Action::make('downloadInvoicePdf')
                            ->label('Invoice')
                            ->icon('heroicon-o-document-currency-dollar')
                            ->action(fn (\App\Models\Quotation $record) => self::downloadPdf($record, 'invoice')),
                        
                        Action::make('milestonePayments')
                            ->label('Milestone Payments')
                            ->icon('heroicon-o-currency-dollar')
                            ->color('info')
                            ->modalHeading('Milestone Payments & Receipts')
                            ->modalWidth('6xl')
                            ->fillForm(fn ($record) => [
                                'milestones' => $record->milestones->toArray(),
                            ])
                            ->form(fn ($record) => [
                                \Filament\Forms\Components\Repeater::make('milestones')
                                    // ->relationship('milestones') // Removed to avoid automatic saving conflicts
                                    ->addable(false)
                                    ->deletable(false)
                                    ->columns(5)
                                    ->schema([
                                        \Filament\Forms\Components\Hidden::make('id'),
                                        TextInput::make('label')
                                            ->label('Milestone')
                                            ->readOnly(),
                                        TextInput::make('amount')
                                            ->label('Amount')
                                            ->readOnly()
                                            ->prefix('฿'),
                                        TextInput::make('status')
                                            ->label('Status')
                                            ->readOnly()
                                            ->formatStateUsing(fn ($state) => $state === 'Paid' ? 'Waiting for admin approval' : $state)
                                            ->extraAttributes(fn ($state) => [
                                                'class' => match($state) {
                                                    'Paid' => 'text-warning-600 font-bold',
                                                    'Approved' => 'text-success-600 font-bold',
                                                    'Rejected' => 'text-danger-600 font-bold',
                                                    default => 'text-gray-500',
                                                }
                                            ]),
                                        \Filament\Forms\Components\FileUpload::make('receipt_path')
                                            ->label('Upload Receipt')
                                            ->disk('public')
                                            ->directory('receipts')
                                            ->visibility('public')
                                            ->visible(fn ($get) => auth()->user()->role === 'sales' && in_array($get('status'), ['Pending', 'Rejected']))
                                            ->helperText(fn() => auth()->user()->role === 'sales' ? 'Note: You MUST click Submit below after uploading the receipt to update the status.' : null),
                                        Placeholder::make('receipt_link')
                                            ->label('Receipt File')
                                            ->content(fn($get) => filled($get('receipt_path')) 
                                                ? new \Illuminate\Support\HtmlString('<a href="'.asset('storage/'.$get('receipt_path')).'" target="_blank" class="text-primary-600 underline font-bold">View Receipt</a>')
                                                : 'No receipt uploaded')
                                            ->visible(fn($get) => filled($get('receipt_path'))),
                                        Select::make('admin_action')
                                            ->label('Admin Action')
                                            ->placeholder('Process Payment...')
                                            ->options([
                                                'Approved' => 'Approve',
                                                'Rejected' => 'Reject',
                                            ])
                                            // ->dehydrated(false) // Removed to ensure it is in the $data array
                                            ->live()
                                            ->visible(fn ($get) => auth()->user()->role === 'admin' && filled($get('receipt_path'))),
                                        Placeholder::make('download_note')
                                            ->label('Action')
                                            ->visible(fn($get) => auth()->user()->role === 'admin' && $get('status') === 'Approved')
                                            ->content('Milestone approved! Close this and use the "Download Milestone Receipt" action to get the invoice.'),
                                    ])
                            ])
                            ->action(function ($record, array $data) {
                                $milestonesData = $data['milestones'] ?? [];
                                foreach ($milestonesData as $mItem) {
                                    $mId = $mItem['id'] ?? null;
                                    if (!$mId) continue;
                                    
                                    $milestone = \App\Models\QuotationMilestone::find($mId);
                                    if (!$milestone) continue;

                                    $receiptPath = $mItem['receipt_path'] ?? null;

                                    // Sales user uploading receipt
                                    if (auth()->user()->role === 'sales' && filled($receiptPath)) {
                                        // Only update if status is Pending or Rejected
                                        if (in_array($milestone->status, ['Pending', 'Rejected'])) {
                                            $milestone->update([
                                                'receipt_path' => $receiptPath,
                                                'status' => 'Paid',
                                                'paid_at' => now(),
                                            ]);

                                            // Notify Admins
                                            $admins = \App\Models\User::where('role', 'admin')->get();
                                            $salesPerson = auth()->user()->name;
                                            $quotationNo = $record->quotation_number;
                                            
                                            \Filament\Notifications\Notification::make()
                                                ->title('Payment Receipt Uploaded')
                                                ->body("{$salesPerson} uploaded a receipt for {$milestone->label} (Quotation: {$quotationNo})")
                                                ->icon('heroicon-o-currency-dollar')
                                                ->color('info')
                                                ->sendToDatabase($admins)
                                                ->send(); // Toast for immediate feedback
                                        }
                                    }

                                    // Admin approving/rejecting
                                    $adminAction = $mItem['admin_action'] ?? null;
                                    if (auth()->user()->role === 'admin' && filled($adminAction)) {
                                        $milestone->update([
                                            'status' => $adminAction,
                                            'approved_at' => $adminAction === 'Approved' ? now() : null,
                                            'approved_by' => $adminAction === 'Approved' ? auth()->id() : null,
                                        ]);

                                        // Ensure record relations are loaded
                                        $record->loadMissing(['project.customer.user', 'customer.user']);

                                        // Notify Sales User
                                        $projectUser = $record->project?->customer?->user;
                                        $customerUser = $record->customer?->user;
                                        $salesUser = $projectUser ?? $customerUser;
                                        
                                        if ($salesUser) {
                                            \Filament\Notifications\Notification::make()
                                                ->title($adminAction === 'Approved' ? 'Payment Approved' : 'Payment Rejected')
                                                ->body("Your payment for {$milestone->label} has been {$adminAction}. (Quotation: {$record->quotation_number})")
                                                ->icon($adminAction === 'Approved' ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                                                ->color($adminAction === 'Approved' ? 'success' : 'danger')
                                                ->sendToDatabase($salesUser)
                                                ->send(); // Toast
                                        }
                                    }
                                }
                                
                                Notification::make()
                                    ->title('Payments Updated')
                                    ->success()
                                    ->send();
                            }),

                        Action::make('downloadMilestoneReceipt')
                            ->label('Download Milestone Receipt')
                            ->icon('heroicon-o-receipt-refund')
                            ->modalHeading('Download Approved Milestone Receipt')
                            ->form(fn ($record) => [
                                Select::make('milestone_id')
                                    ->label('Select Milestone')
                                    ->options($record->milestones()->where('status', 'Approved')->pluck('label', 'id'))
                                    ->required()
                            ])
                            ->action(fn ($record, $data) => self::downloadPdf($record, 'receipt_milestone', (int)$data['milestone_id'])),
                    ])
                        ->label('PDF & Payments')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('gray')
                        ->button(),

                    Action::make('requestCustomMilestones')
                        ->label('Request Custom Milestones')
                        ->icon('heroicon-o-adjustments-horizontal')
                        ->color('warning')
                        ->visible(fn (\App\Models\Quotation $record) => auth()->user()->role === 'sales' && in_array($record->status, ['Draft', 'Approved']))
                        ->form([
                            \Filament\Forms\Components\Repeater::make('requested_milestones')
                                ->label('Custom Breakdown')
                                ->schema([
                                    TextInput::make('label')->required()->placeholder('e.g. 30% Advance'),
                                    TextInput::make('percentage')->numeric()->required()->minValue(1)->maxValue(100)->suffix('%'),
                                ])
                                ->columns(2)
                                ->default([
                                    ['label' => 'Advance', 'percentage' => 30],
                                    ['label' => 'Mid', 'percentage' => 30],
                                    ['label' => 'Mid 2', 'percentage' => 30],
                                    ['label' => 'Final', 'percentage' => 10],
                                ])
                                ->rules([
                                    function () {
                                        return function (string $attribute, $value, \Closure $fail) {
                                            $total = collect($value)->sum('percentage');
                                            if (round($total) != 100) {
                                                $fail("Total percentage must be exactly 100%. Current: {$total}%");
                                            }
                                        };
                                    }
                                ])
                        ])
                        ->action(function (\App\Models\Quotation $record, array $data) {
                            $record->update([
                                'custom_milestone_request' => $data['requested_milestones'],
                                'milestone_request_status' => 'Pending'
                            ]);

                            $admins = \App\Models\User::where('role', 'admin')->get();
                            \Filament\Notifications\Notification::make()
                                ->title('Custom Milestone Request')
                                ->body(auth()->user()->name . " requested a custom milestone breakdown for {$record->formatted_reference}.")
                                ->icon('heroicon-o-adjustments-horizontal')
                                ->color('warning')
                                ->sendToDatabase($admins)
                                ->send();

                            \Filament\Notifications\Notification::make()
                                ->title('Request Sent')
                                ->success()
                                ->send();
                        }),

                    Action::make('reviewCustomMilestones')
                        ->label('Review Milestone Request')
                        ->icon('heroicon-o-adjustments-horizontal')
                        ->color('warning')
                        ->visible(fn (\App\Models\Quotation $record) => auth()->user()->role === 'admin' && !empty($record->custom_milestone_request))
                        ->form(fn (\App\Models\Quotation $record) => [
                            Placeholder::make('info')
                                ->label('')
                                ->content('Sales requested the following milestone breakdown for this quotation:'),
                            \Filament\Forms\Components\Repeater::make('requested_milestones')
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
                        ->action(function (\App\Models\Quotation $record, array $data) {
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

                            $record->update([
                                'custom_milestone_request' => null,
                                'milestone_request_status' => $data['admin_action'] === 'approve' ? 'Approved' : 'Rejected'
                            ]);

                            $salesUser = $record->project?->customer?->user ?? $record->customer?->user;
                            if ($salesUser) {
                                \Filament\Notifications\Notification::make()
                                    ->title('Milestone Request ' . $statusStr)
                                    ->body("Your custom milestone request for {$record->formatted_reference} was {$statusStr}.")
                                    ->icon($icon)
                                    ->color($color)
                                    ->sendToDatabase($salesUser)
                                    ->send();
                            }

                            \Filament\Notifications\Notification::make()
                                ->title("Request {$statusStr}")
                                ->success()
                                ->send();
                        }),

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

    public static function downloadPdf(\App\Models\Quotation $quotation, string $documentType = 'quotation', ?int $milestoneId = null): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $documentType = in_array($documentType, ['quotation', 'invoice', 'receipt_partial', 'receipt_full', 'receipt_milestone'], true)
            ? $documentType
            : 'quotation';

        if ($documentType === 'receipt_milestone' && !$milestoneId) {
            abort(400, 'Milestone ID is required for milestone receipt.');
        }

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
            'milestoneId' => $milestoneId,
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
