<?php

namespace App\Filament\Resources\Customers\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\Placeholder::make('customer_number')
                    ->label('Customer ID')
                    ->content(fn ($record) => $record?->customer_number ?? 'NEW')
                    ->columnSpanFull(),

                TextInput::make('name')
                    ->label('Customer Name')
                    ->placeholder('Enter full name')
                    ->required()
                    ->maxLength(255)
                    ->autocomplete(false),

                TextInput::make('mobile')
                    ->label('Mobile Number')
                    ->placeholder('Enter 10-digit mobile number')
                    ->tel()
                    ->required()
                    ->length(10)
                    ->numeric()
                    ->autocomplete(false),

                Textarea::make('address')
                    ->label('Complete Address')
                    ->placeholder('Enter full address with landmarks')
                    ->required()
                    ->rows(3)
                    ->columnSpanFull()
                    ->autocomplete(false),

                \Filament\Forms\Components\Hidden::make('user_id')
                    ->default(auth()->id())
                    ->required(),
            ])
            ->columns(2);
    }
}
