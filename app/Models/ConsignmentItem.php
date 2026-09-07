<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsignmentItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'consignment_id',
        'product_id',
        'product_variant_id',
        'qty_total',
        'qty_terpakai',
        'harga_satuan',
    ];

    public function consignment()
    {
        return $this->belongsTo(Consignment::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}
