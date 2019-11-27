<?php

namespace App;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;

class Trade extends Model
{
    use UsesTenantConnection;

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
