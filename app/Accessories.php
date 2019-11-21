<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Accessories extends Model
{
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
