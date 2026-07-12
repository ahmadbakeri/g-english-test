<?php

namespace App\Filament\Resources\Finance\Expenses\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

use Filament\Forms\Components\Select;

class ExpenseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('date')
                    ->required(),
                Select::make('category')
                    ->options([
                        'Operasional' => 'Operasional',
                        'Peralatan' => 'Peralatan',
                        'Tagihan' => 'Tagihan',
                        'Gaji' => 'Gaji',
                        'Lain-lain' => 'Lain-lain',
                    ])
                    ->required()
                    ->default('Operasional'),
                TextInput::make('amount')
                    ->required()
                    ->numeric(),
                Textarea::make('description')
                    ->columnSpanFull(),
            ]);
    }
}
