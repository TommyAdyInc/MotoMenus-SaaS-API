<?php

namespace App;

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
}
