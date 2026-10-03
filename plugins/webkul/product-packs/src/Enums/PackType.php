<?php

namespace Webkul\ProductPack\Enums;

use Filament\Support\Contracts\HasLabel;

enum PackType: string implements HasLabel
{
    case DETAILED = 'detailed';

    case NON_DETAILED = 'non_detailed';

    public function getLabel(): string
    {
        return match ($this) {
            self::DETAILED     => __('product_packs::enums/pack-type.detailed'),
            self::NON_DETAILED => __('product_packs::enums/pack-type.non_detailed'),
        };
    }
}
