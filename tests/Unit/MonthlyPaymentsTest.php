<?php

namespace Tests\Unit;

use App\MotoMenus\PaymentsCalculation;
use Tests\TestCase;

class MonthlyPaymentsTest extends TestCase
{
    /** @test * */
    function monthly_payments_returns_array_with_correct_payments_for_each_month()
    {
        $amount = 259;
        $rate = 13.49;
        $down_payments = [];

        $payments = new PaymentsCalculation($down_payments, $rate, $amount);
        $result = $payments->getPayments();

        $this->assertEquals(15.97, $result[18][0]);

        $this->assertEquals(3.64, $result[144][0]);
    }
}
