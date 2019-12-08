<?php

namespace App;

use App\MotoMenus\PaymentsCalculation;
use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;

class Accessories extends Model
{
    use UsesTenantConnection;

    protected $fillable = [
        'part_number',
        'item_name',
        'description',
        'quantity',
        'msrp',
        'unit_price',
        'labor'
    ];

    public static function monthlyPayments($sum, $rate, $months)
    {
        $payments = new PaymentsCalculation([], $rate, $sum);

        return collect($payments->getPayments())->only($months);
    }
}
