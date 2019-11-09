<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Trade extends Model
{
    protected $fillable = [
        'vin',
        'year',
        'make',
        'model',
        'model_number',
        'color',
        'odometer',
        'book_value',
        'trade_in_value',
    ];

    public function deal()
    {
        return $this->belongsTo(Deal::class);
    }
}
