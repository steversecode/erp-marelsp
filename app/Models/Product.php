<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use HasFactory;

    // Product Types (Odoo Standard: Goods, Service, Combo)
    public const TYPE_GOODS = 'goods';
    public const TYPE_SERVICE = 'service';
    public const TYPE_COMBO = 'combo';

    // Traceability Options (Odoo 3 Tracking Options)
    public const TRACKING_SERIAL = 'serial';      // By Unique Serial Number
    public const TRACKING_LOT = 'lot';            // By Lots
    public const TRACKING_QUANTITY = 'quantity';  // By Quantity

    // Invoicing Policy (Odoo Standard)
    public const INVOICING_ORDERED = 'ordered';      // Ordered quantities
    public const INVOICING_DELIVERED = 'delivered';  // Delivered quantities

    // Costing Methods (Odoo Accounting)
    public const COSTING_AVERAGE = 'average';
    public const COSTING_FIFO = 'fifo';
    public const COSTING_STANDARD = 'standard';

    protected $fillable = [
        // General Info
        'code',
        'barcode',
        'name',
        'image',
        'type',
        'category_id',
        'responsible_id',
        'category',
        'is_active',
        'created_by',
        'updated_by',
        'can_be_sold',
        'can_be_purchased',
        'can_be_manufactured',
        'can_be_subcontracted',
        
        // Units & Pricing & Invoicing
        'uom',
        'uom_po',
        'cost_price',
        'sale_price',
        'invoicing_policy',
        
        // Inventory & Traceability Tab
        'track_inventory',
        'tracking',
        'allow_negative_stock',
        'route_buy',
        'route_manufacture',
        'route_subcontract',
        'route_mto',
        'route_resupply_ids',
        'warehouse_id',
        'min_stock',
        'max_stock',
        'reorder_qty',
        'weight',
        'volume',
        'lead_time_days',
        'purchase_lead_time_days',
        
        // Purchase Tab
        'preferred_vendor',
        'vendor_code',
        'purchase_description',
        
        // Accounting & Notes Tab
        'costing_method',
        'valuation_method',
        'description',
        'internal_notes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'can_be_sold' => 'boolean',
        'can_be_purchased' => 'boolean',
        'can_be_manufactured' => 'boolean',
        'can_be_subcontracted' => 'boolean',
        'track_inventory' => 'boolean',
        'allow_negative_stock' => 'boolean',
        'route_buy' => 'boolean',
        'route_manufacture' => 'boolean',
        'route_subcontract' => 'boolean',
        'route_mto' => 'boolean',
        'route_resupply_ids' => 'array',
        'cost_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'min_stock' => 'decimal:3',
        'max_stock' => 'decimal:3',
        'reorder_qty' => 'decimal:3',
        'weight' => 'decimal:3',
        'volume' => 'decimal:4',
        'lead_time_days' => 'integer',
        'purchase_lead_time_days' => 'integer',
    ];

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function bomItems(): HasMany
    {
        return $this->hasMany(BomItem::class);
    }

    public function boms(): HasMany
    {
        return $this->hasMany(Bom::class);
    }

    public function substitutes(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::class,
            'product_substitutes',
            'primary_product_id',
            'substitute_product_id'
        )->withPivot('conversion_rate', 'notes')->withTimestamps();
    }

    public function substitutedFor(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::class,
            'product_substitutes',
            'substitute_product_id',
            'primary_product_id'
        )->withPivot('conversion_rate', 'notes')->withTimestamps();
    }

    public function incomingMoves(): HasMany
    {
        return $this->hasMany(StockMove::class, 'product_id')->whereNotNull('to_location_id');
    }

    public function outgoingMoves(): HasMany
    {
        return $this->hasMany(StockMove::class, 'product_id')->whereNotNull('from_location_id');
    }

    public function moves(): HasMany
    {
        return $this->hasMany(StockMove::class, 'product_id');
    }

    public function productCategory(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function responsible(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsible_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Category display name with fallback to legacy string category
    public function getCategoryNameAttribute(): string
    {
        return $this->productCategory?->name ?? $this->category ?? '-';
    }

    // Image URL Accessor
    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image) {
            return null;
        }

        if (str_starts_with($this->image, 'http')) {
            return $this->image;
        }

        return asset('storage/' . $this->image);
    }

    // Odoo Standard English Labels (Goods, Service, Combo)
    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            self::TYPE_GOODS, 'raw_material', 'work_in_progress', 'finished_good' => 'Goods',
            self::TYPE_SERVICE => 'Service',
            self::TYPE_COMBO => 'Combo',
            default => ucfirst(str_replace('_', ' ', $this->type ?? 'Goods')),
        };
    }

    public function getTrackingLabelAttribute(): string
    {
        return match ($this->tracking ?? 'quantity') {
            self::TRACKING_SERIAL => 'By Unique Serial Number',
            self::TRACKING_LOT => 'By Lots',
            default => 'By Quantity',
        };
    }

    public function getInvoicingPolicyLabelAttribute(): string
    {
        return match ($this->invoicing_policy ?? 'ordered') {
            self::INVOICING_DELIVERED => 'Delivered quantities',
            default => 'Ordered quantities',
        };
    }
}
