<?php

namespace App;

use Hyn\Tenancy\Traits\UsesSystemConnection;
use Illuminate\Database\Eloquent\Model;

class GlobalSetting extends Model
{
    use UsesSystemConnection;

    protected $fillable = [
        'document_fee'
    ];
}
