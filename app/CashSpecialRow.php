<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CashSpecialRow extends Model
{
    protected $fillable = [
        'msrp',
        'discount',
    ];

    public function row_name()
    {
        return $this->belongsTo(CashSpecialRowName::class);
    }
}
