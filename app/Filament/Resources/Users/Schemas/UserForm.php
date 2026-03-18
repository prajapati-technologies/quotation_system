<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('User Details')
                    ->description('Manage user credentials, roles, and access status.')
                    ->icon('heroicon-o-user-circle')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->placeholder('Full Name')
                            ->required(),
                        TextInput::make('email')
                            ->label('Email Address')
                            ->email()
                            ->unique(ignoreRecord: true)
                            ->required(),
                        \Filament\Forms\Components\Select::make('role')
                            ->options([
                                'admin' => 'Admin (Full Access)',
                                'sales' => 'Sales User (Limited Access)',
                            ])
                            ->required()
                            ->native(false)
                            ->default('sales'),
                        Toggle::make('is_active')
                            ->label('Account Active')
                            ->default(true)
                            ->inline(false),
                        TextInput::make('password')
                            ->label('Access Password')
                            ->password()
                            ->dehydrated(fn($state) => filled($state))
                            ->required(fn(string $context): bool => $context === 'create')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
