<?php

namespace App;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseInformation extends Model
{
    use UsesTenantConnection;

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
        'taxable_show_msrp_on_pdf',
        'taxable_price',
        'taxable_manufacturer_freight',
        'taxable_technician_setup',
        'taxable_accessories',
        'taxable_accessories_labor',
        'taxable_labor',
        'taxable_riders_edge_course',
        'taxable_miscellaneous_costs',
        'taxable_document_fee',
        'tax_credit_on_trade'
    ];

    public function deal() :belongsTo
    {
        return $this->belongsTo(Deal::class);
    }
}
