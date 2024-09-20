<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Administrator extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $fillable = [
        "name",
        "email",
        "phone",
        "password",
        "status"
    ];

    protected $hidden = ['password'];

    protected $roleMap = [
        'admin' => 1,
        'casher' => 2,
        'chef' => 3,
    ];

    public function setRoleAttribute($value)
    {
        $this->attributes['role'] = $this->roleMap[$value] ?? 2;
    }

    protected $reverseRoleMap = [
        1 => 'admin',
        2 => 'casher',
        3 => 'chef',
    ];

    public function getRoleAttribute($value)
    {
        return $this->reverseRoleMap[$value] ?? 'chef';
    }

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
