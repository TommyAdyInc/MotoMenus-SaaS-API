<?php

namespace App;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;

class FinanceInsurance extends Model
{
    use UsesTenantConnection;

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

    public function getPreferredAttribute()
    {
        return $this->full_protection + $this->tire_wheel + $this->gap_coverage + $this->theft + $this->priority_maintenance + $this->appearnce_protection;
    }

    public function getStandardAttribute()
    {
        return $this->limited_protection + $this->tire_wheel;
    }
}
