<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuktiBank extends Model
{
    use HasFactory;

    protected $table = 'bukti_banks';

    protected $fillable = [
        'jenis', 'no_bukti', 'tanggal', 'pihak', 'bank_account_id',
        'no_invoice', 'no_po', 'bg_cheque_no', 'keterangan_utama',
        'total', 'terbilang', 'status', 'created_by',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'total' => 'integer',
    ];

    /**
     * Check if transaction can be edited (only draft)
     */
    public function isEditable(): bool
    {
        return $this->status === 'draft';
    }

    public function details()
    {
        return $this->hasMany(BuktiBankDetail::class)->orderBy('urutan');
    }

    public function bankAccount()
    {
        return $this->belongsTo(NoPerkiraan::class, 'bank_account_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeBbm($query) { return $query->where('jenis', 'BBM'); }
    public function scopeBbk($query) { return $query->where('jenis', 'BBK'); }

    public static function generateNoBukti(string $jenis): string
    {
        $prefix = $jenis . '-' . date('Ym') . '-';
        $last = static::where('jenis', $jenis)
            ->where('no_bukti', 'like', $prefix . '%')
            ->orderByDesc('no_bukti')
            ->value('no_bukti');
        $next = $last ? (int) substr($last, -4) + 1 : 1;
        return $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);
    }
}
