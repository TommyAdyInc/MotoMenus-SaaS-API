<?php

namespace App\Http\Controllers;

class PaymentMonthsController extends Controller
{
    public function index()
    {
        try {
            return response()->json(config('payment_months'), 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
