<?php

namespace App;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable, UsesTenantConnection, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'role',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function customers() :HasMany
    {
        return $this->hasMany(Customer::class);
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
