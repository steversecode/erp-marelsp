<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UomCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function uoms()
    {
        return $this->hasMany(Uom::class, 'category_id');
    }

    public function referenceUom()
    {
        return $this->hasOne(Uom::class, 'category_id')->where('uom_type', 'reference');
    }
}
