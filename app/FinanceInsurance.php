<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FinanceInsurance extends Model
{
    protected $fillable = [
        'cash_down_payment',
        'preferred_standard_rate',
        'preferred_standard_term',
        'promotional_rate',
        'promotional_term',
        'full_protection',
        'limited_protection',
        'tire_wheel',
        'gap_coverage',
        'theft',
        'priority_maintenance',
        'appearance_protection',
    ];
}
