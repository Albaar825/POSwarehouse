<?php

namespace App\Models;

use Illuminate\Auth\Passwords\CanResetPassword as CanResetPasswordTrait;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements CanResetPassword
{
    use Notifiable, CanResetPasswordTrait;

    protected $fillable = [
        'name',
        'email',
        'password',
        'password_changed_at',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'password_changed_at' => 'datetime',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isKasir(): bool
    {
        return $this->role === 'kasir';
    }

    public function canChangePassword(): bool
    {
        if (! $this->password_changed_at) {
            return true;
        }

        return $this->password_changed_at->addDays(7)->isPast();
    }

    public function daysUntilCanChangePassword(): int
    {
        if (! $this->password_changed_at) {
            return 0;
        }

        $availableAt = $this->password_changed_at->addDays(7);

        return $availableAt->isPast() ? 0 : now()->diffInDays($availableAt, false) + 1;
    }
}
