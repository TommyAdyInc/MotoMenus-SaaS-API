<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Unit extends Model
{
    protected $fillable = [
        'stock_number',
        'year',
        'make',
        'model',
        'model_number',
        'color',
        'odometer',
    ];

    public function deal() :belongsTo
    {
        return $this->belongsTo(Deal::class);
    }
}
