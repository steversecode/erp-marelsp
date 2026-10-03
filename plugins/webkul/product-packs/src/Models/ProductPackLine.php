<?php

namespace Webkul\ProductPack\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Product\Models\Product;

class ProductPackLine extends Model
{
    use HasFactory;

    protected $table = 'products_product_pack_lines';

    protected $fillable = [
        'parent_product_id',
        'product_id',
        'quantity',
    ];

    protected $casts = [
        'quantity' => 'float',
    ];

    public function parentProduct(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'parent_product_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function getSubtotalPriceAttribute(): float
    {
        return round(($this->product?->price ?? 0) * (float) $this->quantity, 2);
    }
}
