<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuktiBankDetail extends Model
{
    use HasFactory;

    protected $table = 'bukti_bank_details';

    protected $fillable = [
        'bukti_bank_id', 'no_perkiraan_id', 'kode_perkiraan',
        'nama_perkiraan', 'keterangan', 'jumlah', 'urutan',
    ];

    protected $casts = ['jumlah' => 'integer'];

    public function buktiBank()
    {
        return $this->belongsTo(BuktiBank::class);
    }

    public function noPerkiraan()
    {
        return $this->belongsTo(NoPerkiraan::class);
    }
}
