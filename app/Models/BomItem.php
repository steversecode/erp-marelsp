<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BomItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'bom_id',
        'sequence',
        'product_id',
        'quantity',
        'cones',
        'uom',
        'wastage_percent',
        'notes',
    ];

    protected $casts = [
        'sequence' => 'integer',
        'quantity' => 'decimal:4',
        'cones' => 'decimal:2',
        'wastage_percent' => 'decimal:2',
    ];

    public function bom(): BelongsTo
    {
        return $this->belongsTo(Bom::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
