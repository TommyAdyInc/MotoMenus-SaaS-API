<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StoreNameController extends Controller
{
    public function update()
    {
        request()->validate([
            'name' => ['required', 'min:3'],
        ]);

        try {
            $website = app(\Hyn\Tenancy\Environment::class)->website();

            $website->update(['store_name' => request()->get('name')]);

            return response()->json(true, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
