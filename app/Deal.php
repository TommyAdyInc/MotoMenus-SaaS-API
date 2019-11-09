<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Deal extends Model
{
    protected $fillable = [
        'customer_id',
        'user_id',
    ];

    protected $with = [
        'accessories',
        'finance_insurance',
        'payment_schedule',
        'purchase_information',
        'units',
        'trades',
    ];

    public function accessories(): hasMany
    {
        return $this->hasMany(Accessories::class);
    }

    public function customer(): belongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function finance_insurance(): hasOne
    {
        return $this->hasOne(FinanceInsurance::class);
    }

    public function payment_schedule(): hasOne
    {
        return $this->hasOne(PaymentSchedule::class);
    }

    public function purchase_information(): hasOne
    {
        return $this->hasOne(PurchaseInformation::class);
    }

    public function trades(): hasMany
    {
        return $this->hasMany(Trade::class);
    }

    public function units(): hasMany
    {
        return $this->hasMany(Unit::class);
    }

    public function user(): belongsTo
    {
        return $this->belongsTo(User::class);
    }


    public function scopeCanGetAll($query)
    {
        if (isAdmin() && request()->has('user_id') && auth()->id() != request()->get('user_id')) {
            $query->whereUserId(request()->get('user_id'));
        } elseif (!isAdmin()) {
            $query->whereUserId(auth()->id());
        }

        return $query;
    }

    public function scopeFilter($query)
    {
        if (request()->has('id') && !empty(request()->get('id'))) {
            return $query->whereId(request()->get('id'));
        }

        if (request()->has('sale_status') && !empty(request()->get('sale_status'))) {
            $query->whereSaleStatus(request()->get('sale_status'));
        }

        if (request()->has('customer_type') && !empty(request()->get('customer_type'))) {
            $query->where(function ($q) {
                collect(request()->get('customer_type'))
                    ->each(function ($type) use (&$q) {
                        $q->orWhere('customer_type', 'like', '%' . $type . '%');
                    });
            });
        }

        if(request()->has('unit') && !empty(request()->get('unit'))) {
            $query->whereHas('units', function ($q) {
                collect(request()->get('unit'))->each(function($val, $key) use (&$q) {
                    $q->where($key, 'LIKE', '%' . $val);
                });
            });
        }

        if(request()->has('trade') && !empty(request()->get('trade'))) {
            $query->whereHas('trades', function ($q) {
                collect(request()->get('trade'))->each(function($val, $key) use (&$q) {
                    $q->where($key, 'LIKE', '%' . $val);
                });
            });
        }

        if(request()->has('customer') && !empty(request()->get('customer'))) {
            $query->whereHas('customer', function ($q) {
                collect(request()->get('customer'))->each(function($val, $key) use (&$q) {
                    $q->where($key, 'LIKE', '%' . $val);
                });
            });
        }

        return $query;
    }
}
