<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StoreSetting extends Model
{
    protected $fillable = ['default_interest_rate', 'default_tax_rate'];
}
