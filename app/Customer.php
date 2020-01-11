<?php

namespace App;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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

    public function deals(): hasMany
    {
        return $this->hasMany(Deal::class);
    }

    public function note(): MorphOne
    {
        return $this->morphOne(Note::class, 'notable');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeCanSeeAll($query)
    {
        if (!isAdmin() && !auth()->user()->isSuperAdmin()) {
            $query->whereUserId(auth()->id());
        }

        return $query;
    }

    public function scopeFilter($query)
    {
        // loose filtering using request
        collect($this->fillable)
            ->each(function ($field) use (&$query) {
                if ($this->requestHas($field)) {
                    if ($field == 'user_id') {
                        $query->whereUserId($field);
                    } else {
                        $query->where($field, 'LIKE', '%' . request()->get($field) . '%');
                    }
                }
            });

        return $query;
    }

    private function requestHas($field)
    {
        return request()->has($field) && !empty(request()->get($field));
    }

    public function getNameAttribute()
    {
        return $this->first_name . (!empty($this->middle_name) ? (' ' . $this->middle_name[0]) : '') . ' ' . $this->last_name;
    }

    public function getCityStateAttribute()
    {
        return $this->city . ', ' . $this->state . ' ' . $this->postcode;
    }
}
