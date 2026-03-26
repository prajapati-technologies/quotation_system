<?php

namespace App\Filament\Resources\Projects\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\Select::make('customer_id')
                    ->label('Customer Name')
                    ->relationship('customer', 'name', modifyQueryUsing: fn ($query) => auth()->user()->role === 'sales' ? $query->where('user_id', auth()->id()) : $query)
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('name')
                    ->label('Project Name')
                    ->required(),
                \Filament\Forms\Components\Select::make('type')
                    ->options([
                        'New home' => 'New home',
                        'Renovation home' => 'Renovation home',
                        'Construction company' => 'Construction company',
                        'Developer' => 'Developer',
                        'Resaler' => 'Resaler',
                    ])
                    ->required(),
                DatePicker::make('expected_delivery_date')
                    ->label('Expiry Date')
                    ->required(),
            ]);
    }
}
