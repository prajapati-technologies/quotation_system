<?php

namespace App\Filament\Pages;

use App\Models\Quotation;
use App\Models\QuotationMilestone;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Url;

class SalesReport extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.pages.sales-report';

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-presentation-chart-bar';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Reports';
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public function getTitle(): string
    {
        return 'Sales Analytics';
    }

    public static function getNavigationLabel(): string
    {
        return 'Sales Report';
    }

    #[Url]
    public ?string $salesPersonId = null;

    #[Url]
    public ?string $startDate = null;

    #[Url]
    public ?string $endDate = null;

    public function mount()
    {
        $this->startDate = $this->startDate ?? now()->startOfMonth()->toDateString();
        $this->endDate = $this->endDate ?? now()->endOfMonth()->toDateString();
        
        $this->form->fill([
            'salesPersonId' => $this->salesPersonId,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
        ]);
    }

    public static function canAccess(): bool
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }

    public function getSalesUsers(): array
    {
        return User::where('role', 'sales')->pluck('name', 'id')->toArray();
    }

    protected function getFormSchema(): array
    {
        return [
            Select::make('salesPersonId')
                ->label('Sales Representative')
                ->options($this->getSalesUsers())
                ->placeholder('All Sales Team')
                ->live()
                ->afterStateUpdated(fn ($state) => $this->salesPersonId = $state),
            DatePicker::make('startDate')
                ->label('From Date')
                ->live()
                ->afterStateUpdated(fn ($state) => $this->startDate = $state),
            DatePicker::make('endDate')
                ->label('To Date')
                ->live()
                ->afterStateUpdated(fn ($state) => $this->endDate = $state),
        ];
    }

    public function form($form)
    {
        return $form
            ->schema($this->getFormSchema())
            ->columns(3);
    }

    protected function refreshWidgets(): void
    {
        // Livewire updates public properties automatically, 
        // and widgets receive them in getHeaderWidgets()/getFooterWidgets().
    }
}
