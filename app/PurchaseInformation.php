<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseInformation extends Model
{
    protected $fillable = [
        'msrp',
        'price',
        'manufacturer_freight',
        'technician_setup',
        'accessories',
        'accessories_labor',
        'labor',
        'riders_edge_course',
        'miscellaneous_costs',
        'document_fee',
        'trade_in_allowance',
        'sales_tax_rate',
        'payoff_balance_owed',
        'title_trip_fee',
        'deposit',
        'taxables',
    ];

    protected $casts = [
        'taxables' => 'array'
    ];

    public function deal() :belongsTo
    {
        return $this->belongsTo(Deal::class);
    }
}
