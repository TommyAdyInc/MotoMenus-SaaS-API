<?php

namespace App;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;

class CashSpecialRowName extends Model
{
    use UsesTenantConnection;

    public function column()
    {
        return $this->belongsTo(CashSpecial::class);
    }
}
