<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SalesStepController extends Controller
{
    public function index()
    {
        try {
            return response()->json(config('sale_status'), 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
