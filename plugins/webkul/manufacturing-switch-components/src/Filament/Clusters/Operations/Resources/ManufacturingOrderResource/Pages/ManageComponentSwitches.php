<?php

namespace Webkul\ManufacturingSwitchComponents\Filament\Clusters\Operations\Resources\ManufacturingOrderResource\Pages;

use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Livewire\Livewire;
use Webkul\Manufacturing\Filament\Clusters\Operations\Resources\ManufacturingOrderResource;
use Webkul\ManufacturingSwitchComponents\Filament\Actions\SwitchComponentAction;
use Webkul\ManufacturingSwitchComponents\Models\ComponentSwitch;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ManageComponentSwitches extends ManageRelatedRecords
{
    use HasRecordNavigationTabs;

    protected static string $resource = ManufacturingOrderResource::class;

    protected static string $relationship = 'componentSwitches';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrows-right-left';

    public static function getNavigationLabel(): string
    {
        return __('manufacturing_switch_components::app.navigation.title');
    }

    public static function getNavigationBadge($parameters = []): ?string
    {
        try {
            $record = Livewire::current()?->getRecord();

            if (! $record) {
                return null;
            }

            $count = $record->componentSwitches()->count();

            return $count > 0 ? (string) $count : null;
        } catch (\Throwable) {
            return null;
        }
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')
                    ->label(__('manufacturing_switch_components::app.table.columns.date'))
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('originalProduct.name')
                    ->label(__('manufacturing_switch_components::app.table.columns.original_product'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('original_quantity')
                    ->label(__('manufacturing_switch_components::app.table.columns.original_quantity'))
                    ->formatStateUsing(fn (ComponentSwitch $record): string => sprintf('%.4f %s', (float) $record->original_quantity, $record->originalUom?->name ?? ''))
                    ->alignEnd(),

                TextColumn::make('replacementProduct.name')
                    ->label(__('manufacturing_switch_components::app.table.columns.replacement_product'))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('warning'),

                TextColumn::make('replacement_quantity')
                    ->label(__('manufacturing_switch_components::app.table.columns.replacement_quantity'))
                    ->formatStateUsing(fn (ComponentSwitch $record): string => sprintf('%.4f %s', (float) $record->replacement_quantity, $record->replacementUom?->name ?? ''))
                    ->alignEnd(),

                TextColumn::make('reason')
                    ->label(__('manufacturing_switch_components::app.table.columns.reason'))
                    ->limit(40)
                    ->tooltip(fn (ComponentSwitch $record): ?string => $record->reason)
                    ->placeholder('—'),

                TextColumn::make('switchedBy.name')
                    ->label(__('manufacturing_switch_components::app.table.columns.switched_by'))
                    ->placeholder('System'),
            ])
            ->headerActions([
                SwitchComponentAction::make(),
            ]);
    }
}
