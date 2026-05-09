<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = ['total_price'];

    // Relasi ke sales_details
    public function salesDetails()
    {
        return $this->hasMany(SalesDetail::class);
    }
}

