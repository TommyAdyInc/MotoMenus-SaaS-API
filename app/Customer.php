<?php

namespace App;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use UsesTenantConnection;

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'address',
        'city',
        'postcode',
        'state',
        'phone',
        'phone2',
        'email',
    ];
}
