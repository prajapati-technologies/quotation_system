<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class MilestoneSettings extends Page implements HasForms
{
    use InteractsWithForms;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-banknotes';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Operations';
    }

    public static function getNavigationSort(): ?int
    {
        return 10;
    }

    public static function getNavigationLabel(): string
    {
        return 'Milestone Template';
    }

    public function getTitle(): string
    {
        return 'Milestone Payment Template';
    }

    protected string $view = 'filament.pages.milestone-settings';

    public array $milestoneTemplate = [];

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->role === 'admin';
    }

    public static function canAccess(): bool
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }

    public function mount(): void
    {
        $raw = Setting::get('milestone_template');
        if ($raw) {
            $decoded = json_decode($raw, true);
            $this->milestoneTemplate = is_array($decoded) ? $decoded : $this->defaultTemplate();
        } else {
            $this->milestoneTemplate = $this->defaultTemplate();
        }

        $this->form->fill([
            'milestoneTemplate' => $this->milestoneTemplate,
        ]);
    }

    private function defaultTemplate(): array
    {
        return [
            ['label' => 'Down Payment',  'percentage' => 50],
            ['label' => 'Mid Payment',   'percentage' => 40],
            ['label' => 'Final Payment', 'percentage' => 10],
        ];
    }

    protected function getFormSchema(): array
    {
        return [
            Repeater::make('milestoneTemplate')
                ->label('Milestone Breakdown')
                ->helperText('Admin can add/remove/reorder milestones. Total percentage must equal 100%.')
                ->schema([
                    TextInput::make('label')
                        ->label('Milestone Name')
                        ->required()
                        ->placeholder('e.g. Down Payment'),
                    TextInput::make('percentage')
                        ->label('Percentage (%)')
                        ->numeric()
                        ->required()
                        ->suffix('%'),
                ])
                ->columns(2)
                ->addActionLabel('Add Milestone')
                ->reorderable()
                ->cloneable(false)
                ->columnSpanFull(),
        ];
    }

    public function form($form): mixed
    {
        return $form
            ->schema($this->getFormSchema())
            ->statePath('milestoneTemplate');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        // $data is the statePath content = milestoneTemplate array
        $milestones = is_array($data) ? array_values($data) : [];

        $total = collect($milestones)->sum(fn($m) => floatval($m['percentage'] ?? 0));

        if ((int) round($total) !== 100) {
            Notification::make()
                ->title('Validation Error')
                ->body("Total percentage must be exactly 100%. Current total: {$total}%")
                ->danger()
                ->send();
            return;
        }

        Setting::updateOrCreate(
            ['key' => 'milestone_template'],
            ['value' => json_encode($milestones)]
        );

        // Sync back to property
        $this->milestoneTemplate = $milestones;

        Notification::make()
            ->title('Saved!')
            ->body('Milestone template updated. New quotations will use this breakdown.')
            ->success()
            ->send();
    }
}
