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
        'status_pembayaran',
        'metode_pembayaran',
        'use_ppn',
        'invoice_number',
        'surat_jalan_number',
        'nama_pasien',
        'operator',
        'tanggal_operasi',
        'alamat',
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

    // Relasi ke Payments (pembayaran)
    public function payments()
    {
        return $this->hasMany(Payment::class, 'transaction_id')->where('transaction_type', 'sale');
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
