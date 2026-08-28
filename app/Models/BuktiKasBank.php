<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuktiKasBank extends Model
{
    use HasFactory;

    protected $table = 'bukti_kas_bank';

    protected $fillable = [
        'jenis',
        'no_bukti',
        'tanggal',
        'pihak',
        'keterangan_utama',
        'bank_ac_no',
        'bg_cheque_no',
        'total',
        'terbilang',
        'created_by',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'total' => 'integer',
    ];

    /**
     * Relasi ke detail item
     */
    public function details()
    {
        return $this->hasMany(BuktiKasBankDetail::class)->orderBy('urutan');
    }

    /**
     * Relasi ke user yang membuat
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope untuk BKK (Bukti Kas/Bank Keluar)
     */
    public function scopeBkk($query)
    {
        return $query->where('jenis', 'BKK');
    }

    /**
     * Scope untuk BKM (Bukti Kas/Bank Masuk)
     */
    public function scopeBkm($query)
    {
        return $query->where('jenis', 'BKM');
    }

    /**
     * Generate auto number: BKK-YYYYMM-0001 / BKM-YYYYMM-0001
     */
    public static function generateNoBukti(string $jenis): string
    {
        $prefix = $jenis . '-' . date('Ym') . '-';

        $last = static::where('jenis', $jenis)
            ->where('no_bukti', 'like', $prefix . '%')
            ->orderByDesc('no_bukti')
            ->value('no_bukti');

        if ($last) {
            $lastNumber = (int) substr($last, -4);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }
}
