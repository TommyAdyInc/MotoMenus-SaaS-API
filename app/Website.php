<?php


namespace App;

use Carbon\Carbon;
use Hyn\Tenancy\Models\Website as WebsiteAlias;
use Hyn\Tenancy\Traits\UsesSystemConnection;

class Website extends WebsiteAlias
{
    use UsesSystemConnection;

    protected $fillable = [
        'store_name',
    ];

    protected $appends = [
        'date_entered'
    ];

    public function getDateEnteredAttribute() {
        return Carbon::createFromFormat('Y-m-d H:i:s', $this->created_at)->format('m-d-Y');
    }
}
