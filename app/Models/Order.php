<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'customer_name',
        'total_price',
        'ordered_at',
        'status_pembayaran',   // sudah ditambahkan
        'metode_pembayaran',   // tambahkan ini juga
    ];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke OrderItem
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
