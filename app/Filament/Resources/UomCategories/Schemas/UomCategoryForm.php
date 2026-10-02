<?php

namespace App\Filament\Resources\UomCategories\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UomCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
            ]);
    }
}
