<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'supplier_name',
        'alamat',
        'total_price',
        'ordered_at',
        'status',
        'use_ppn',
        'po_number',
        'catatan',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    // Relasi ke Payments (pembayaran)
    public function payments()
    {
        return $this->hasMany(Payment::class, 'transaction_id')->where('transaction_type', 'purchase');
    }

    public function getGrandTotalAttribute()
    {
        return $this->total_price;
    }

    public function getTotalPaidAttribute()
    {
        return $this->payments()->sum('amount');
    }

    public function getRemainingAttribute()
    {
        return max(0, $this->grand_total - $this->total_paid);
    }

    public function getPaymentStatusAttribute()
    {
        $paid = $this->total_paid;
        if ($paid >= $this->grand_total) {
            return 'lunas';
        }
        return $paid > 0 ? 'belum lunas' : 'belum dibayar';
    }
}
