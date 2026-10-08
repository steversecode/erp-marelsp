<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources;

use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Resources\ParentResourceRegistration;
use Filament\Tables\Table;
use Webkul\Manufacturing\Filament\Clusters\Operations\Resources\ManufacturingOrderResource as BaseManufacturingOrderResource;
use Webkul\Manufacturing\Models\Order as ManufacturingOrder;
use Webkul\Sale\Filament\Clusters\Orders;

class QuotationManufacturingOrderResource extends BaseManufacturingOrderResource
{
    protected static ?string $model = ManufacturingOrder::class;

    protected static ?string $parentResource = QuotationResource::class;

    protected static ?string $slug = 'manufacturing';

    protected static ?string $recordTitleAttribute = 'name';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $cluster = Orders::class;

    public static function canAccess(): bool
    {
        $parentResource = static::$parentResource;

        return $parentResource::canAccess();
    }

    public static function getParentResourceRegistration(): ?ParentResourceRegistration
    {
        return QuotationResource::asParent()
            ->relationship('manufacturingOrders');
    }

    public static function table(Table $table): Table
    {
        return BaseManufacturingOrderResource::table($table)
            ->recordUrl(fn ($record): string => BaseManufacturingOrderResource::getUrl('view', ['record' => $record]))
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->url(fn ($record): string => BaseManufacturingOrderResource::getUrl('view', ['record' => $record])),
                    EditAction::make()
                        ->url(fn ($record): string => BaseManufacturingOrderResource::getUrl('edit', ['record' => $record])),
                ]),
            ]);
    }
}
