<?php

namespace App;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Customer extends Model
{
    use UsesTenantConnection;

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'address',
        'city',
        'postcode',
        'state',
        'phone',
        'phone2',
        'email',
        'user_id',
    ];

    protected $with = ['note', 'user'];

    public function note(): MorphOne
    {
        return $this->morphOne(Note::class, 'notable');
    }

    public function user() :BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
