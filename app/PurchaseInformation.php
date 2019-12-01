<?php

namespace App;

use App\MotoMenus\PaymentsCalculation;
use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseInformation extends Model
{
    use UsesTenantConnection;

    protected $fillable = [
        'msrp',
        'price',
        'manufacturer_freight',
        'technician_setup',
        'accessories',
        'accessories_labor',
        'labor',
        'riders_edge_course',
        'miscellaneous_costs',
        'document_fee',
        'trade_in_allowance',
        'sales_tax_rate',
        'payoff_balance_owed',
        'title_trip_fee',
        'deposit',
        'taxable_show_msrp_on_pdf',
        'taxable_price',
        'taxable_manufacturer_freight',
        'taxable_technician_setup',
        'taxable_accessories',
        'taxable_accessories_labor',
        'taxable_labor',
        'taxable_riders_edge_course',
        'taxable_miscellaneous_costs',
        'taxable_document_fee',
        'tax_credit_on_trade'
    ];

    public function deal(): belongsTo
    {
        return $this->belongsTo(Deal::class);
    }

    public function getSalesTaxAttribute()
    {
        $total = 0;

        collect([
            'taxable_price',
            'taxable_manufacturer_freight',
            'taxable_technician_setup',
            'taxable_accessories',
            'taxable_accessories_labor',
            'taxable_labor',
            'taxable_riders_edge_course',
            'taxable_miscellaneous_costs',
            'taxable_document_fee',
        ])->each(function ($field) use (&$total) {
            if ($this->{$field}) {
                $total += $this->{substr($field, 8)};
            }
        });

        if ($this->tax_credit_on_trade && $this->taxable_price) {
            $total -= $this->trade_in_allowance;
        }

        return $total * $this->sales_tax_rate / 100;
    }

    public function getSubTotalAttribute()
    {
        return collect([
            'price',
            'manufacturer_freight',
            'technician_setup',
            'accessories',
            'accessories_labor',
            'labor',
            'riders_edge_course',
            'miscellaneous_costs',
            'document_fee',
            'trade_in_allowance',
        ])->reduce(function ($total, $field) {
            $total += $field == 'trade_in_allowance' ? -$this->{$field} : $this->{$field};

            return $total;
        }, 0);
    }

    public function getCashBalanceAttribute()
    {
        return $this->sub_total +
            $this->sales_tax +
            $this->payoff_balance_owed +
            $this->title_trip_fee -
            $this->deposit;
    }

    public function FinanceOptions(PaymentSchedule $payment_schedule)
    {
        $payments = new PaymentsCalculation($payment_schedule->payment_options['down_payment_options'], $payment_schedule->rate, $this->cash_balance);

        return collect($payments->getPayments())->only($payment_schedule->payment_options['months']);
    }
}
