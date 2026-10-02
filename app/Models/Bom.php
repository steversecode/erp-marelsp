<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bom extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'type',
        'product_id',
        'quantity',
        'uom',
        'notes',
        'ready_to_produce',
        'consumption',
        'produce_delay',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'is_active' => 'boolean',
        'produce_delay' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(BomItem::class)->orderBy('sequence')->orderBy('id');
    }

    public function manufacturingOrders(): HasMany
    {
        return $this->hasMany(ManufacturingOrder::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'phantom' => 'Kit',
            default => 'Manufacture this product',
        };
    }

    public function getTotalCostAttribute(): float
    {
        $total = 0;
        foreach ($this->items as $item) {
            if ($item->product) {
                $wastage = 1 + (($item->wastage_percent ?? 0) / 100);
                $total += (float) $item->quantity * (float) $item->product->cost_price * $wastage;
            }
        }
        return $total;
    }
}
