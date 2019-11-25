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
        'customer_type',
        'sales_status',
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

    public function addRelatedModules()
    {
        // add any Units to deal
        if (request()->has('units')) {
            collect(request()->get('units'))->each(function ($unit) {
                if ($this->hasFilledFields($unit)) {
                    $this->units()->create($unit);
                }
            });
        }

        // add any Trades to deal
        if (request()->has('trades')) {
            collect(request()->get('trades'))->each(function ($trade) {
                if ($this->hasFilledFields($trade)) {
                    $this->trades()->create($trade);
                }
            });
        }

        // Add any accessories to deal
        if (request()->has('accessories')) {
            collect(request()->get('accessories'))->each(function ($acc) {
                if ($this->hasFilledFields($acc)) {
                    $this->accessories()->create($acc);
                }
            });
        }

        // add Purchase information to deal
        // TODO: retrieve document fee specified in system options
        if (request()->has('purchase_information')) {
            if ($this->hasFilledFields(request()->get('purchase_information'))) {
                $this->purchase_information()->create(array_merge(
                        collect(request()->get('purchase_information'))->except('document_fee')->all(),
                        ['document_fee' => 259])
                );
            }
        }

        // add payment schedule to deal
        if (request()->has('payment_schedule')) {
            if ($this->hasFilledFields(request()->get('payment_schedule'))) {
                $this->payment_schedule()->create(request()->get('payment_schedule'));
            }
        }

        // add F&I to deal
        if (request()->has('finance_insurance')) {
            if ($this->hasFilledFields(request()->get('finance_insurance'))) {
                $this->finance_insurance()->create(request()->get('finance_insurance'));
            }
        }
    }

    public function updateRelatedModules()
    {
        // add and update any Units to deal
        if (request()->has('units')) {
            collect(request()->get('units'))->each(function ($unit) {
                if ($this->hasFilledFields($unit)) {
                    if (isset($unit['id'])) {
                        $this->units()->where('units.id', $unit['id'])->update($unit);
                    } else {
                        $this->units()->create($unit);
                    }
                }
            });
        }

        // add and update any Trades to deal
        if (request()->has('trades')) {
            collect(request()->get('trades'))->each(function ($trade) {
                if ($this->hasFilledFields($trade)) {
                    if (isset($trade['id'])) {
                        $this->trades()->where('trades.id', $trade['id'])->update($trade);
                    } else {
                        $this->trades()->create($trade);
                    }
                }
            });
        }

        // Add any accessories to deal
        if (request()->has('accessories')) {
            collect(request()->get('accessories'))->each(function ($acc) {
                if ($this->hasFilledFields($acc)) {
                    if (isset($acc['id'])) {
                        $this->accessories()->where('accessories.id', $acc['id'])->update($acc);
                    } else {
                        $this->accessories()->create($acc);
                    }
                }
            });
        }

        // add/update Purchase information to deal
        if (request()->has('purchase_information')) {
            if ($this->hasFilledFields(request()->get('purchase_information'))) {
                $this->purchase_information()->updateOrCreate(request()->get('purchase_information.id') ?? [],
                    request()->get('purchase_information'));
            }
        }

        // add/update payment schedule to deal
        if (request()->has('payment_schedule')) {
            if ($this->hasFilledFields(request()->get('payment_schedule'))) {
                $this->payment_schedule()->updateOrCreate(request()->get('payment_schedule.id') ?? [],
                    request()->get('payment_schedule'));
            }
        }

        // add F&I to deal
        if (request()->has('finance_insurance')) {
            if ($this->hasFilledFields(request()->get('finance_insurance'))) {
                $this->finance_insurance()->updateOrCreate(request()->get('finance_insurance.id') ?? [],
                    request()->get('finance_insurance'));
            }
        }
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

        if (request()->has('unit') && !empty(request()->get('unit'))) {
            $query->whereHas('units', function ($q) {
                collect(request()->get('unit'))->each(function ($val, $key) use (&$q) {
                    $q->where($key, 'LIKE', '%' . $val);
                });
            });
        }

        if (request()->has('trade') && !empty(request()->get('trade'))) {
            $query->whereHas('trades', function ($q) {
                collect(request()->get('trade'))->each(function ($val, $key) use (&$q) {
                    $q->where($key, 'LIKE', '%' . $val);
                });
            });
        }

        if (request()->has('customer') && !empty(request()->get('customer'))) {
            $query->whereHas('customer', function ($q) {
                collect(request()->get('customer'))->each(function ($val, $key) use (&$q) {
                    $q->where($key, 'LIKE', '%' . $val);
                });
            });
        }

        return $query;
    }


    public function setCustomerTypeAttribute($value)
    {
        $this->attributes['customer_type'] = json_encode($value);
    }

    private function hasFilledFields($array)
    {
        return collect($array)->reduce(function ($carry, $a) {
            $carry = $carry || !empty($a);

            return $carry;
        }, false);
    }
}
