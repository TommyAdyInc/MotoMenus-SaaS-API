<?php

namespace App;

use Laravel\Passport\AuthCode;

class PassportAuthCode extends AuthCode
{
    protected $connection = 'tenant';
}
