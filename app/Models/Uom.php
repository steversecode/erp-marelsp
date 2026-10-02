<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Uom extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'uom_type',
        'ratio',
        'rounding',
        'is_active',
    ];

    protected $casts = [
        'ratio' => 'float',
        'rounding' => 'float',
        'is_active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(UomCategory::class, 'category_id');
    }

    /**
     * Relationship to products that use this UoM (by uom text match or FK if present)
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'uom', 'name');
    }

    /**
     * Get human-readable UoM Type label
     */
    public function getTypeLabelAttribute(): string
    {
        return match ($this->uom_type) {
            'reference' => 'Reference Unit of Measure for this category',
            'smaller' => 'Smaller than the reference Unit of Measure',
            'bigger' => 'Bigger than the reference Unit of Measure',
            default => 'Reference Unit of Measure',
        };
    }

    /**
     * Get Ratio description formula relative to reference unit
     */
    public function getRatioFormulaAttribute(): string
    {
        $refUom = $this->category?->referenceUom ?? $this->category?->uoms()->where('uom_type', 'reference')->first();
        $refName = $refUom ? $refUom->name : 'Unit';

        if ($this->uom_type === 'reference') {
            return "1.0 {$this->name} (Base Reference)";
        }

        if ($this->uom_type === 'bigger') {
            $val = (float)$this->ratio;
            return "1 {$this->name} = {$val} {$refName}";
        }

        if ($this->uom_type === 'smaller') {
            $val = (float)$this->ratio;
            $inv = $val > 0 ? (1 / $val) : 0;
            return "1 {$this->name} = {$val} {$refName} (1 {$refName} = " . round($inv, 4) . " {$this->name})";
        }

        return "{$this->ratio}";
    }
}
