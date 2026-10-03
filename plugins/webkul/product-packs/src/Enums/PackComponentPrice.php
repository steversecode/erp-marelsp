<?php

namespace Webkul\ProductPack\Enums;

use Filament\Support\Contracts\HasLabel;

enum PackComponentPrice: string implements HasLabel
{
    case DETAILED = 'detailed';

    case TOTALIZED = 'totalized';

    case IGNORED = 'ignored';

    public function getLabel(): string
    {
        return match ($this) {
            self::DETAILED  => __('product_packs::enums/pack-component-price.detailed'),
            self::TOTALIZED => __('product_packs::enums/pack-component-price.totalized'),
            self::IGNORED   => __('product_packs::enums/pack-component-price.ignored'),
        };
    }
}
