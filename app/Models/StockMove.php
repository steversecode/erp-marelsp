<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMove extends Model
{
    use HasFactory;

    public const CAT_PO_RECEIPT = 'po_receipt';
    public const CAT_PRODUCTION_RETURN = 'production_return';
    public const CAT_MO_CONSUMPTION = 'mo_consumption';
    public const CAT_MO_FINISHED_GOODS = 'mo_finished_goods';
    public const CAT_SAMPLE_ISSUE = 'sample_issue';
    public const CAT_DYEING_OUT = 'dyeing_out';
    public const CAT_DYEING_RETURN = 'dyeing_return';
    public const CAT_ADJUSTMENT = 'adjustment';

    protected $fillable = [
        'move_number',
        'product_id',
        'from_location_id',
        'to_location_id',
        'qty',
        'uom',
        'category',
        'reference_type',
        'reference_id',
        'reference_number',
        'batch_lot_number',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'qty' => 'decimal:4',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function fromLocation(): BelongsTo
    {
        return $this->belongsTo(StockLocation::class, 'from_location_id');
    }

    public function toLocation(): BelongsTo
    {
        return $this->belongsTo(StockLocation::class, 'to_location_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getCategoryBadgeAttribute(): string
    {
        return match ($this->category) {
            self::CAT_PO_RECEIPT => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            self::CAT_PRODUCTION_RETURN => 'bg-teal-50 text-teal-700 border-teal-200',
            self::CAT_MO_CONSUMPTION => 'bg-indigo-50 text-indigo-700 border-indigo-200',
            self::CAT_MO_FINISHED_GOODS => 'bg-blue-50 text-blue-700 border-blue-200',
            self::CAT_SAMPLE_ISSUE => 'bg-amber-50 text-amber-700 border-amber-200',
            self::CAT_DYEING_OUT => 'bg-purple-50 text-purple-700 border-purple-200',
            self::CAT_DYEING_RETURN => 'bg-fuchsia-50 text-fuchsia-700 border-fuchsia-200',
            default => 'bg-slate-50 text-slate-700 border-slate-200',
        };
    }

    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            self::CAT_PO_RECEIPT => 'Penerimaan PO',
            self::CAT_PRODUCTION_RETURN => 'Retur Sisa Produksi',
            self::CAT_MO_CONSUMPTION => 'Konsumsi Produksi (MO)',
            self::CAT_MO_FINISHED_GOODS => 'Output Barang Jadi (MO)',
            self::CAT_SAMPLE_ISSUE => 'Pengeluaran Sample',
            self::CAT_DYEING_OUT => 'Kirim Proses Celup',
            self::CAT_DYEING_RETURN => 'Kembali Dari Celup',
            default => 'Penyesuaian Stok',
        };
    }
}
