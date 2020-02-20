<?php

namespace App\Http\Controllers;

use App\MotoMenus\PaymentsCalculation;
use Illuminate\Http\Request;

class CalculatePaymentsController extends Controller
{
    public function store()
    {
        request()->validate([
            'down_payments' => ['required', 'array'],
            'rate'          => ['required', 'numeric', 'min:0.01'],
            'amount'        => ['required', 'min:1'],
        ]);

        try {
            $payments = new PaymentsCalculation(request()->get('down_payments'), request()->get('rate'),
                request()->get('amount'));

            return response()->json($payments->getPayments(), 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
