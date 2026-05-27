<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_type',
        'transaction_id',
        'party_type',
        'party_name',
        'payment_date',
        'amount',
        'note',
        'created_by',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];

    // Relasi ke User pembuat
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Mendapatkan relasi transaksi secara manual (polymorphic manual)
    public function transaction()
    {
        if ($this->transaction_type === 'purchase') {
            return $this->belongsTo(PurchaseOrder::class, 'transaction_id');
        }
        return $this->belongsTo(Order::class, 'transaction_id');
    }
}
