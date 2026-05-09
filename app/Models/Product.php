<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'kode_barang', 'vendor', 'stock', 'satuan', 'price', 'image', 'category'];    // Relasi ke sales_details

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }
    public function salesDetails()
    {
        return $this->hasMany(SalesDetail::class);
    }

    // Relasi ke purchases
    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
}

