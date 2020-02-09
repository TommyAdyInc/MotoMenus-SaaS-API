<?php

namespace App;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;

class CashSpecialRow extends Model
{
    use UsesTenantConnection;

    protected $fillable = [
        'msrp',
        'discount',
    ];

    public function row_name()
    {
        return $this->belongsTo(CashSpecialRowName::class);
    }
}
