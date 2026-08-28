<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuktiKasDetail extends Model
{
    use HasFactory;

    protected $table = 'bukti_kas_details';

    protected $fillable = [
        'bukti_kas_id',
        'no_perkiraan_id',
        'no_account',
        'nama_perkiraan',
        'keterangan',
        'jumlah',
        'urutan',
    ];

    protected $casts = [
        'jumlah' => 'integer',
    ];

    /**
     * Relasi ke bukti kas induk
     */
    public function buktiKas()
    {
        return $this->belongsTo(BuktiKas::class);
    }

    /**
     * Relasi ke No. Perkiraan
     */
    public function noPerkiraan()
    {
        return $this->belongsTo(NoPerkiraan::class);
    }
}
