<?php

namespace App\Http\Controllers;

use App\GlobalSetting;
use Illuminate\Http\Request;

class GlobalSettingController extends Controller
{
    public function index()
    {
        try {
            return response()->json(GlobalSetting::first(), 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function update()
    {
        request()->validate([
            'document_fee' => ['numeric', 'min:0']
        ]);

        try {
            $setting = GlobalSetting::first();

            return response()->json($setting->update(request()->all()), 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
