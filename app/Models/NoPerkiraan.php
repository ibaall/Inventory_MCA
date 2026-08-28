<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NoPerkiraan extends Model
{
    use HasFactory;

    protected $table = 'no_perkiraans';

    protected $fillable = ['kode_perkiraan', 'nama_perkiraan'];

    /**
     * Label untuk dropdown: "100 - Kas"
     */
    public function getLabelAttribute(): string
    {
        return $this->kode_perkiraan . ' - ' . $this->nama_perkiraan;
    }
}
