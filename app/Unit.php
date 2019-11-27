<?php

namespace App;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Unit extends Model
{
    use UsesTenantConnection;

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

    public function purchase_information(): hasOne
    {
        return $this->hasOne(PurchaseInformation::class);
    }
}
