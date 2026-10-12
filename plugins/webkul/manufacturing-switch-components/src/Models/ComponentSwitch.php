<?php

namespace Webkul\ManufacturingSwitchComponents\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Manufacturing\Models\Move;
use Webkul\Manufacturing\Models\Order;
use Webkul\Manufacturing\Models\Product;
use Webkul\Security\Models\User;
use Webkul\Support\Models\UOM;

class ComponentSwitch extends Model
{
    use HasFactory;

    protected $table = 'manufacturing_component_switches';

    protected $fillable = [
        'order_id',
        'original_move_id',
        'original_product_id',
        'original_quantity',
        'original_uom_id',
        'replacement_move_id',
        'replacement_product_id',
        'replacement_quantity',
        'replacement_uom_id',
        'reason',
        'switched_by_id',
    ];

    protected $casts = [
        'original_quantity'    => 'decimal:4',
        'replacement_quantity' => 'decimal:4',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function originalMove(): BelongsTo
    {
        return $this->belongsTo(Move::class, 'original_move_id');
    }

    public function originalProduct(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'original_product_id')->withTrashed();
    }

    public function originalUom(): BelongsTo
    {
        return $this->belongsTo(UOM::class, 'original_uom_id');
    }

    public function replacementMove(): BelongsTo
    {
        return $this->belongsTo(Move::class, 'replacement_move_id');
    }

    public function replacementProduct(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'replacement_product_id')->withTrashed();
    }

    public function replacementUom(): BelongsTo
    {
        return $this->belongsTo(UOM::class, 'replacement_uom_id');
    }

    public function switchedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'switched_by_id');
    }
}
