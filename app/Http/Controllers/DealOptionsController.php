<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DealOptionsController extends Controller
{
    public function index()
    {
        try {
            return response()->json([
                'customer_types' => config('customer_types'),
                'payment_months' => config('payment_months'),
                'sale_statuses'  => config('sale_status'),
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
