<?php


namespace App;

use Hyn\Tenancy\Models\Website as WebsiteAlias;
use Hyn\Tenancy\Traits\UsesSystemConnection;

class Website extends WebsiteAlias
{
    use UsesSystemConnection;

    protected $fillable = [
        'store_name',
    ];
}
