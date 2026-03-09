<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'first_name', 'last_name', 'email', 'cnic', 'phone',
        'nationality', 'password', 'otp', 'otp_expires_at',
    ];

    protected $hidden = ['password', 'remember_token', 'otp'];

    protected function casts(): array
    {
        return [
            'phone_verified_at'  => 'datetime',
            'email_verified_at'  => 'datetime',
            'otp_expires_at'     => 'datetime',
            'password'           => 'hashed',
        ];
    }

    // ── Relationships ───────────────────────────────────────
    public function candidate()
    {
        return $this->hasOne(Candidate::class);
    }

    // ── Helpers ─────────────────────────────────────────────
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function isOtpValid(): bool
    {
        return $this->otp && $this->otp_expires_at && $this->otp_expires_at->isFuture();
    }

    public function generateOtp(): string
    {
        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $this->update(['otp' => $otp, 'otp_expires_at' => now()->addMinutes(10)]);
        return $otp;
    }

    public function clearOtp(): void
    {
        $this->update(['otp' => null, 'otp_expires_at' => null]);
    }
}
