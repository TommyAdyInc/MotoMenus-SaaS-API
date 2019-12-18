<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CashSpecialRowName extends Model
{
    public function column()
    {
        return $this->belongsTo(CashSpecial::class);
    }
}
