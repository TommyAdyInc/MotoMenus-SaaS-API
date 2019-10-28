<?php

namespace App;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    use UsesTenantConnection;

    protected $fillable = [
        'role',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
