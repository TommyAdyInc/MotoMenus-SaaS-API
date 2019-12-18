<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CashSpecial extends Model
{
    public function columns()
    {
        return $this->hasMany(CashSpecialColumn::class);
    }

    public function row_names()
    {
        return $this->hasMany(CashSpecialRowName::class);
    }
}
