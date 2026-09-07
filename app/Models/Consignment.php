<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'surat_jalan_number',
        'customer_name',
        'status',
        'created_by',
    ];

    public function items()
    {
        return $this->hasMany(ConsignmentItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
