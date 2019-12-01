<?php
/**
 * Created by PhpStorm.
 * User: Paul
 * Date: 11/21/2019
 * Time: 10:10 AM
 */

namespace App\MotoMenus;


class PaymentsCalculation
{
    private $down_payments;
    private $rate;
    private $amount;
    private $months;

    /**
     * PaymentsCalculation constructor.
     *
     * @param array $down_payments (array of down payment options i.e. [1000, 5000, 8000]
     * @param       $rate (as a percentage %)
     * @param       $amount (total amount before down payments deducted)
     */
    public function __construct(Array $down_payments, $rate, $amount)
    {
        $this->down_payments = !empty($down_payments) ? collect($down_payments) : collect(0);
        $this->rate = $rate;
        $this->amount = $amount;
        $this->months = collect(config('payment_months'));
    }

    /**
     * @return array
     */
    public function getPayments()
    {
        $payments = [];

        $periodic_interest = ($this->rate / 12) / 100; // rate is annual rate

        $this->months->each(function ($month) use (&$payments, $periodic_interest) {
            $discount_factor = (pow(1 + $periodic_interest,
                        $month) * $periodic_interest) / (pow(1 + $periodic_interest, $month) - 1);

            $monthly = [];
            $this->down_payments->each(function ($d) use (&$monthly, $discount_factor) {
                $monthly[$d] = round(($this->amount - $d) * $discount_factor, 2);
            });

            $payments[$month] = $monthly;
        });

        return $payments;
    }
}
