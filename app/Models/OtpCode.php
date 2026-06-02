<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtpCode extends Model
{
    protected $fillable = ['user_id', 'code', 'expires_at', 'is_used'];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'is_used' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check apakah OTP sudah expired.
     */
    public function isExpired(): bool
    {
        return now()->greaterThan($this->expires_at);
    }

    /**
     * Check apakah OTP masih valid (belum expired dan belum dipakai).
     */
    public function isValid(): bool
    {
        return !$this->is_used && !$this->isExpired();
    }
}
