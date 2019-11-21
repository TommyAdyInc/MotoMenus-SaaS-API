<?php

namespace App\Http\Controllers;

use App\StoreSetting;

class StoreSettingController extends Controller
{
    public function index()
    {
        try {
            return response()->json(StoreSetting::first(), 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function update(StoreSetting $store_setting)
    {
        request()->validate([
            'default_interest_rate' => ['number', 'min:0.01'],
            'default_tax_rate'      => ['number', 'min:0.01'],
        ]);

        try {
            return response()->json(tap($store_setting)->update(request()->all()), 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
