<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MoComponent extends Model
{
    use HasFactory;

    protected $fillable = [
        'manufacturing_order_id',
        'original_product_id',
        'actual_product_id',
        'is_switched',
        'switch_reason',
        'switched_by',
        'switched_at',
        'planned_qty',
        'issued_qty',
        'returned_qty',
        'uom',
    ];

    protected $casts = [
        'is_switched' => 'boolean',
        'switched_at' => 'datetime',
        'planned_qty' => 'decimal:4',
        'issued_qty' => 'decimal:4',
        'returned_qty' => 'decimal:4',
    ];

    public function manufacturingOrder(): BelongsTo
    {
        return $this->belongsTo(ManufacturingOrder::class);
    }

    public function originalProduct(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'original_product_id');
    }

    public function actualProduct(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'actual_product_id');
    }

    public function switchedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'switched_by');
    }

    /**
     * Get actual net consumption (Issued - Returned)
     */
    public function getNetConsumedQtyAttribute(): float
    {
        return (float) ($this->issued_qty - $this->returned_qty);
    }
}
