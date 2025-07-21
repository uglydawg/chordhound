<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhoneVerification extends Model
{
    protected $fillable = [
        'phone_number',
        'code',
        'expires_at',
        'used_at',
        'attempts',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'used_at' => 'datetime',
        ];
    }

    public static function generateFor(string $phoneNumber): self
    {
        // Delete any existing codes for this phone number
        static::where('phone_number', $phoneNumber)->delete();

        $code = str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

        return static::create([
            'phone_number' => $phoneNumber,
            'code' => $code,
            'expires_at' => now()->addMinutes(10),
            'attempts' => 0,
        ]);
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isUsed(): bool
    {
        return ! is_null($this->used_at);
    }

    public function markAsUsed(): void
    {
        $this->update(['used_at' => now()]);
    }

    public function incrementAttempts(): void
    {
        $this->increment('attempts');
    }

    public function tooManyAttempts(): bool
    {
        return $this->attempts >= 3;
    }
}
