<?php

namespace App;

use Laravel\Passport\Token;

class PassportToken extends Token
{
    protected $connection = 'tenant';
}
