<?php

namespace App\Filament\Resources\Uoms\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UomForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('category_id')
                    ->required()
                    ->numeric(),
                TextInput::make('name')
                    ->required(),
                Select::make('uom_type')
                    ->options(['reference' => 'Reference', 'smaller' => 'Smaller', 'bigger' => 'Bigger'])
                    ->default('reference')
                    ->required(),
                TextInput::make('ratio')
                    ->required()
                    ->numeric()
                    ->default(1.0),
                TextInput::make('rounding')
                    ->required()
                    ->numeric()
                    ->default(0.001),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
