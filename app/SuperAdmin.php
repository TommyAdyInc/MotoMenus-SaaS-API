<?php

namespace App;

use Hyn\Tenancy\Traits\UsesSystemConnection;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\HasApiTokens;

class SuperAdmin extends Authenticatable
{
    use HasApiTokens, Notifiable;
    use UsesSystemConnection;

    protected $fillable = [
        'email',
        'name',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function isSuperAdmin()
    {
        return true;
    }

    public static function boot(): void
    {
        static::creating(function ($user) {
            $user->password = !empty($user->password) ? Hash::make($user->password) : null;
        });

        static::updating(function ($user) {
            $user->password = !empty(request()->get('password')) ? Hash::make(request()->get('password')) : $user->password;
        });

        static::deleting(function ($user) {
            // $user->notes()->delete();
        });

        parent::boot();
    }
}
