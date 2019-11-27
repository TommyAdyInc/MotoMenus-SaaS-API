<?php

namespace App;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;

class StoreSetting extends Model
{
    use UsesTenantConnection;

    protected $fillable = ['default_interest_rate', 'default_tax_rate'];
}
