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
                    ->relationship('customer', 'name', modifyQueryUsing: fn ($query) => auth()->user()->role === 'sales' ? $query->where('user_id', auth()->id()) : $query)
                    ->required(),
                TextInput::make('name')
                    ->required(),
                \Filament\Forms\Components\Select::make('type')
                    ->options([
                        'New' => 'New',
                        'Renovation' => 'Renovation',
                    ])
                    ->required(),
                DatePicker::make('expected_delivery_date')
                    ->required(),
            ]);
    }
}
