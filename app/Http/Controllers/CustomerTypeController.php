<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CustomerTypeController extends Controller
{
    public function index()
    {
        try {
            return response()->json(config('customer_types'), 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
