<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductCategory extends Model
{
    use HasFactory;

    protected $table = 'product_categories';

    protected $fillable = [
        'name',
        'code',
        'parent_id',
        'costing_method',
        'valuation_method',
        'description',
    ];

    /**
     * Parent category (Many-to-One / self-relation)
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'parent_id');
    }

    /**
     * Child categories (One-to-Many / sub-categories)
     */
    public function children(): HasMany
    {
        return $this->hasMany(ProductCategory::class, 'parent_id');
    }

    /**
     * Products belonging to this category (One-to-Many)
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'category_id');
    }

    /**
     * Complete hierarchy display name e.g. "Raw Materials / Yarn / Cotton"
     */
    public function getCompleteNameAttribute(): string
    {
        if ($this->parent) {
            return "{$this->parent->complete_name} / {$this->name}";
        }

        return $this->name;
    }
}
