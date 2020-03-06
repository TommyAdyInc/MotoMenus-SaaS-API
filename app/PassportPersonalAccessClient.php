<?php

namespace App;

use Laravel\Passport\PersonalAccessClient;

class PassportPersonalAccessClient extends PersonalAccessClient
{
    protected $connection = 'tenant';
}
