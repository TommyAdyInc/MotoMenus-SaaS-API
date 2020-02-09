<?php

namespace App;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;

class CashSpecial extends Model
{
    use UsesTenantConnection;

    public function columns()
    {
        return $this->hasMany(CashSpecialColumn::class);
    }

    public function row_names()
    {
        return $this->hasMany(CashSpecialRowName::class);
    }
}
