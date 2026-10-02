<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Warehouse extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'address',
        'view_location_id',
        'lot_stock_id',
        'incoming_steps',
        'outgoing_steps',
        'buy_to_resupply',
        'manufacture_to_resupply',
        'manufacture_steps',
        'resupply_from_warehouse_ids',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'buy_to_resupply' => 'boolean',
        'manufacture_to_resupply' => 'boolean',
        'resupply_from_warehouse_ids' => 'array',
        'is_active' => 'boolean',
    ];

    public function lotStock(): BelongsTo
    {
        return $this->belongsTo(StockLocation::class, 'lot_stock_id');
    }

    public function viewLocation(): BelongsTo
    {
        return $this->belongsTo(StockLocation::class, 'view_location_id');
    }

    public function locations(): HasMany
    {
        return $this->hasMany(StockLocation::class, 'warehouse_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get Collection of Warehouses this warehouse resupplies from
     */
    public function getResupplyWarehousesAttribute()
    {
        if (empty($this->resupply_from_warehouse_ids)) {
            return collect();
        }

        return Warehouse::whereIn('id', $this->resupply_from_warehouse_ids)->get();
    }

    /**
     * Get list of generated routes according to Odoo multi-warehouse logic
     */
    public function getGeneratedRoutesAttribute(): array
    {
        $routes = [];
        $stockName = $this->lotStock ? $this->lotStock->name : 'Stock';

        if ($this->buy_to_resupply) {
            $routes[] = [
                'name' => "{$this->name}: Buy",
                'code' => 'buy_' . $this->id,
                'type' => 'buy',
                'description' => "Pengadaan bahan dari vendor ke {$this->name} ({$stockName})",
                'step' => $this->incoming_steps === '1_step' ? '1 step (Direct Receipt)' : ($this->incoming_steps === '2_steps' ? '2 steps (Input -> Stock)' : '3 steps (Input -> QC -> Stock)'),
            ];
        }

        if ($this->manufacture_to_resupply) {
            $mfgStepLabel = match($this->manufacture_steps) {
                '1_step' => '1 step (Manufacture directly)',
                '2_steps' => '2 steps (Pick components then manufacture)',
                '3_steps' => '3 steps (Pick -> Manufacture -> Store)',
                default => '2 steps'
            };

            $routes[] = [
                'name' => "{$this->name}: Manufacture",
                'code' => 'manufacture_' . $this->id,
                'type' => 'manufacture',
                'description' => "Produksi in-house di {$this->name} ({$mfgStepLabel})",
                'step' => $mfgStepLabel,
            ];
        }

        foreach ($this->resupply_warehouses as $parentWh) {
            $parentStockName = $parentWh->lotStock ? $parentWh->lotStock->name : 'Stock';
            $routes[] = [
                'name' => "Supply Product from {$parentWh->name} to {$this->name}",
                'code' => "resupply_{$parentWh->id}_to_{$this->id}",
                'type' => 'resupply',
                'description' => "Internal transfer distribusi dari {$parentWh->name} ({$parentStockName}) ke {$this->name}",
                'step' => 'Internal Transfer (Resupply)',
            ];
        }

        $routes[] = [
            'name' => "Replenish on Order (MTO)",
            'code' => 'mto_' . $this->id,
            'type' => 'mto',
            'description' => "Make To Order: Memulai produksi/pengadaan otomatis saat ada permintaan stok di {$this->name}",
            'step' => 'Pull on Demand',
        ];

        return $routes;
    }
}
