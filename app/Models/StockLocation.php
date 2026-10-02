<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'warehouse_id',
        'parent_id',
        'code',
        'barcode',
        'name',
        'type',
        'is_scrap',
        'is_return',
        'address',
        'comment',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_scrap' => 'boolean',
        'is_return' => 'boolean',
    ];

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(StockLocation::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(StockLocation::class, 'parent_id');
    }

    public function outgoingStockMoves(): HasMany
    {
        return $this->hasMany(StockMove::class, 'from_location_id');
    }

    public function incomingStockMoves(): HasMany
    {
        return $this->hasMany(StockMove::class, 'to_location_id');
    }

    /**
     * Get Complete Odoo Hierarchical Path (e.g. "WH/Stock", "Partner Locations/Vendors")
     */
    public function getCompleteNameAttribute(): string
    {
        if ($this->parent) {
            return $this->parent->complete_name . '/' . $this->name;
        }

        return $this->name;
    }

    /**
     * Get Human-Readable Location Type Label matching Odoo standard
     */
    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'view' => 'View',
            'internal' => 'Internal Location',
            'vendor' => 'Vendor Location',
            'customer' => 'Customer Location',
            'production' => 'Production',
            'inventory', 'loss' => 'Inventory Loss',
            'transit' => 'Transit Location',
            'sample' => 'Sample / R&D',
            'dyeing_subcon' => 'Subcontractor (Dyeing)',
            default => ucfirst($this->type),
        };
    }

    /**
     * Helper Methods to Get or Auto-create Standard Locations
     */
    public static function mainLocationId(): int
    {
        return static::firstOrCreate(
            ['code' => 'WH-MAIN'],
            ['name' => 'Gudang Bahan Baku Utama', 'type' => 'internal', 'address' => 'Gedung A Lantai 1', 'is_active' => true]
        )->id;
    }

    public static function productionLocationId(): int
    {
        return static::firstOrCreate(
            ['code' => 'PROD-FLOOR'],
            ['name' => 'Lantai Produksi (WIP)', 'type' => 'production', 'address' => 'Lantai Kerja Rajut & Jahit', 'is_active' => true]
        )->id;
    }

    public static function finishedGoodsLocationId(): int
    {
        return static::firstOrCreate(
            ['code' => 'WH-FG'],
            ['name' => 'Gudang Barang Jadi (Finished Goods)', 'type' => 'internal', 'address' => 'Gedung B', 'is_active' => true]
        )->id;
    }

    public static function vendorLocationId(): int
    {
        return static::firstOrCreate(
            ['code' => 'VENDOR-SUPP'],
            ['name' => 'Supplier Eksternal', 'type' => 'vendor', 'address' => 'Partner Logistik', 'is_active' => true]
        )->id;
    }

    public static function subconLocationId(): int
    {
        return static::firstOrCreate(
            ['code' => 'SUBCON-DYEING'],
            ['name' => 'Vendor Celup CV Bintang Tex (Subcon)', 'type' => 'dyeing_subcon', 'address' => 'Kawasan Industri Rancaekek', 'is_active' => true]
        )->id;
    }

    public static function sampleLocationId(): int
    {
        return static::firstOrCreate(
            ['code' => 'VIRTUAL-SAMPLE'],
            ['name' => 'Divisi R&D / Pembuatan Sample', 'type' => 'sample', 'address' => 'Studio Desain & Sample', 'is_active' => true]
        )->id;
    }

    public static function scrapLocationId(): int
    {
        return static::firstOrCreate(
            ['code' => 'SCRAP-LOSS'],
            ['name' => 'Virtual Lokasi Afval / Rusak', 'type' => 'loss', 'address' => 'Penampungan Limbah', 'is_scrap' => true, 'is_active' => true]
        )->id;
    }
}
