<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ManufacturingOrder extends Model
{
    use HasFactory;

    protected $table = 'legacy_manufacturing_orders';

    protected $fillable = [
        'mo_number',
        'bom_id',
        'product_id',
        'planned_qty',
        'produced_qty',
        'uom',
        'status',
        'start_date',
        'end_date',
        'source_location_id',
        'destination_location_id',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'planned_qty' => 'decimal:3',
        'produced_qty' => 'decimal:3',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function bom(): BelongsTo
    {
        return $this->belongsTo(Bom::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function components(): HasMany
    {
        return $this->hasMany(MoComponent::class);
    }

    public function sourceLocation(): BelongsTo
    {
        return $this->belongsTo(StockLocation::class, 'source_location_id');
    }

    public function destinationLocation(): BelongsTo
    {
        return $this->belongsTo(StockLocation::class, 'destination_location_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
