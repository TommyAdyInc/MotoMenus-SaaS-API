<?php

namespace App;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;

class CashSpecialColumn extends Model
{
    use UsesTenantConnection;

    public function cash_special()
    {
        return $this->belongsTo(CashSpecial::class);
    }

    public function row_names()
    {
        return $this->hasManyThrough(CashSpecialRowName::class, CashSpecial::class);
    }

    public function rows()
    {
        return $this->hasMany(CashSpecialRow::class);
    }
}
