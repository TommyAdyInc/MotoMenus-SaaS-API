<?php

namespace App;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentSchedule extends Model
{
    use UsesTenantConnection;

    protected $fillable = [
        'rate',
        'show_accessories_payments_on_pdf',
        'payment_options',
    ];

    protected $casts = [
        'payment_options' => 'array'
    ];

    public function deal() :belongsTo
    {
        return $this->belongsTo(Deal::class);
    }
}
