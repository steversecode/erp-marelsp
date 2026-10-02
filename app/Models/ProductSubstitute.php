<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductSubstitute extends Model
{
    use HasFactory;

    protected $fillable = [
        'primary_product_id',
        'substitute_product_id',
        'conversion_rate',
        'notes',
    ];

    protected $casts = [
        'conversion_rate' => 'decimal:4',
    ];

    public function primaryProduct(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'primary_product_id');
    }

    public function substituteProduct(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'substitute_product_id');
    }
}
