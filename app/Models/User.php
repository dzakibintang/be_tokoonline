<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $fillable = [
        'nama',
        'email',
        'kata_sandi',
        'peran',
        'telepon',
        'alamat',
    ];

    protected $hidden = [
        'kata_sandi',
    ];

    // JWT Methods
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    // Override password column
    public function getAuthPassword()
    {
        return $this->kata_sandi;
    }
}
