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

    public function update()
    {
        request()->validate([
            'default_interest_rate' => ['numeric', 'min:0.01'],
            'default_tax_rate'      => ['numeric', 'min:0.01'],
        ]);

        try {
            StoreSetting::first()->update(request()->all());

            return response()->json(true, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
