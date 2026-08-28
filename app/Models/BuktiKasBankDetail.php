<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuktiKasBankDetail extends Model
{
    use HasFactory;

    protected $table = 'bukti_kas_bank_details';

    protected $fillable = [
        'bukti_kas_bank_id',
        'no_account',
        'keterangan',
        'jumlah',
        'urutan',
    ];

    protected $casts = [
        'jumlah' => 'integer',
    ];

    /**
     * Relasi ke bukti kas bank induk
     */
    public function buktiKasBank()
    {
        return $this->belongsTo(BuktiKasBank::class);
    }
}
