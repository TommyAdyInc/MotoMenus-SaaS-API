<?php

namespace App;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    use UsesTenantConnection;

    protected $fillable = ['note'];
}
