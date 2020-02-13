<?php

namespace App;

use App\MotoMenus\PaymentsCalculation;
use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Deal extends Model
{
    use UsesTenantConnection;

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
        'units.purchase_information',
        'trades',
        'user',
        'customer'
    ];

    protected $appends = ['deal_date'];

    public function getCustomerTypeAttribute($value)
    {
        return $value ? json_decode($value):  [];
    }

    public function getDealDateAttribute()
    {
        return $this->created_at->format('m/d/Y');
    }

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

    public function trades(): hasMany
    {
        return $this->hasMany(Trade::class);
    }

    public function units(): hasMany
    {
        return $this->hasMany(Unit::class);
    }

    public function purchase_information()
    {
        return $this->hasManyThrough(PurchaseInformation::class, Unit::class);
    }

    public function user(): belongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function addRelatedModules()
    {
        // add any Units to deal
        if (request()->has('units')) {
            $document_fee = GlobalSetting::first()->document_fee;

            collect(request()->get('units'))->each(function ($unit) use ($document_fee) {
                if ($this->hasFilledFields($unit)) {
                    $u = $this->units()->create($unit);

                    // add Purchase information to unit
                    if (isset($unit['purchase_information'])) {
                        if ($this->hasFilledFields($unit['purchase_information'])) {
                            $u->purchase_information()->create(array_merge(
                                    collect($unit['purchase_information'])->except('document_fee')->all(),
                                    ['document_fee' => $document_fee])
                            );
                        }
                    }
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
            $document_fee = GlobalSetting::first()->document_fee;

            collect(request()->get('units'))->each(function ($unit) use ($document_fee) {
                if ($this->hasFilledFields($unit)) {
                    if (isset($unit['id'])) {
                        $u = tap($this->units()->where('units.id',
                            $unit['id']))->update(collect($unit)->only((new Unit)->getFillable())->all())->first();
                    } else {
                        $u = $this->units()->create($unit);
                    }
                    if ($u && isset($unit['purchase_information'])) {
                        if ($this->hasFilledFields($unit['purchase_information'])) {
                            if (!isset($unit['purchase_information']['id'])) {
                                $u->purchase_information()->create(
                                    array_merge(collect($unit['purchase_information'])->except('document_fee')->all(),
                                        ['document_fee' => $document_fee])
                                );
                            } else {
                                $u->purchase_information()->update(collect($unit['purchase_information'])->except('document_fee')->all());
                            }
                        }
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
        if ((isAdmin() || auth()->user()->isSuperAdmin()) && request()->has('user_id') && auth()->id() != request()->get('user_id')) {
            $query->whereUserId(request()->get('user_id'));
        } elseif (!isAdmin() && !auth()->user()->isSuperAdmin()) {
            $query->whereUserId(auth()->id());
        }

        return $query;
    }

    //
    public function scopeFilter($query)
    {
        if (request()->has('id') && !empty(request()->get('id'))) {
            return $query->whereId(request()->get('id'));
        }

        if (request()->has('sales_status') && !empty(request()->get('sales_status')) && strtolower(request()->get('sales_status')) != 'all') {
            $query->whereSalesStatus(request()->get('sales_status'));
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
                collect(json_decode(request()->get('unit'), true))->each(function ($val, $key) use (&$q) {
                    if (!empty($val)) {
                        $q->where($key, 'LIKE', '%' . $val);
                    }
                });
            });
        }

        if (request()->has('trade') && !empty(request()->get('trade'))) {
            $query->whereHas('trades', function ($q) {
                collect(json_decode(request()->get('trade'), true))->each(function ($val, $key) use (&$q) {
                    if (!empty($val)) {
                        $q->where($key, 'LIKE', '%' . $val);
                    }
                });
            });
        }

        if (request()->has('customer') && !empty(request()->get('customer'))) {
            $query->whereHas('customer', function ($q) {
                collect(json_decode(request()->get('customer'), true))->each(function ($val, $key) use (&$q) {
                    if (!empty($val)) {
                        $q->where($key, 'LIKE', '%' . $val);
                    }
                });
            });
        }

        return $query;
    }

    public function delete()
    {
        $this->units()->delete();
        $this->trades()->delete();
        $this->finance_insurance()->delete();
        $this->payment_schedule()->delete();
        $this->accessories()->delete();
        $this->purchase_information()->delete();

        return parent::delete();
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

    public function getTotalCashBalanceAttribute()
    {
        return $this->units->reduce(function ($total, $unit) {
            $total += round($unit->purchase_information->cash_balance, 2);

            return $total;
        }, 0);
    }

    public function totalFinanceOptions(PaymentSchedule $payment_schedule)
    {
        $payments = new PaymentsCalculation($payment_schedule->payment_options['down_payment_options'],
            $payment_schedule->rate, $this->total_cash_balance);

        return collect($payments->getPayments())->only($payment_schedule->payment_options['months']);
    }
}
