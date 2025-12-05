<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
//use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    const ROLE_SUPER_ADMIN = 1;
    const ROLE_QUALITY_CONTROL = 2;
    const ROLE_OPERATOR = 3;

    protected $fillable = [
        'username',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'role' => 'integer',
    ];

    // Helper methods...
    public function isSuperAdmin(): bool
    {
        return $this->role === self::ROLE_SUPER_ADMIN;
    }
    public function isQualityControl(): bool
    {
        return $this->role === self::ROLE_QUALITY_CONTROL;
    }
    public function isOperator(): bool
    {
        return $this->role === self::ROLE_OPERATOR;
    }
}