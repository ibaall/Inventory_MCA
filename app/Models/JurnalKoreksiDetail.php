<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JurnalKoreksiDetail extends Model
{
    use HasFactory;

    protected $table = 'jurnal_koreksi_details';

    protected $fillable = [
        'jurnal_koreksi_id',
        'no_perkiraan_id',
        'kode_perkiraan',
        'nama_perkiraan',
        'keterangan',
        'debit',
        'kredit',
        'urutan',
    ];

    protected $casts = [
        'debit' => 'integer',
        'kredit' => 'integer',
    ];

    /**
     * Relasi ke jurnal koreksi induk
     */
    public function jurnalKoreksi()
    {
        return $this->belongsTo(JurnalKoreksi::class);
    }

    /**
     * Relasi ke No. Perkiraan
     */
    public function noPerkiraan()
    {
        return $this->belongsTo(NoPerkiraan::class);
    }
}
