<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JurnalKoreksi extends Model
{
    use HasFactory;

    protected $table = 'jurnal_koreksis';

    protected $fillable = [
        'no_jurnal',
        'tanggal',
        'keterangan',
        'status',
        'created_by',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    /**
     * Relasi ke detail jurnal
     */
    public function details()
    {
        return $this->hasMany(JurnalKoreksiDetail::class)->orderBy('urutan');
    }

    /**
     * Relasi ke user yang membuat
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Generate auto number: JK-YYYYMM-0001
     */
    public static function generateNoJurnal(): string
    {
        $prefix = 'JK-' . date('Ym') . '-';

        $last = static::where('no_jurnal', 'like', $prefix . '%')
            ->orderByDesc('no_jurnal')
            ->value('no_jurnal');

        $next = $last ? (int) substr($last, -4) + 1 : 1;
        return $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Total Debit
     */
    public function getTotalDebitAttribute()
    {
        return $this->details->sum('debit');
    }

    /**
     * Total Kredit
     */
    public function getTotalKreditAttribute()
    {
        return $this->details->sum('kredit');
    }
}
