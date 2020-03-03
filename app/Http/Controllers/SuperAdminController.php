<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SuperAdminController extends Controller
{
    public function update()
    {
        request()->validate([
            'password' => ['nullable', 'required']
        ]);

        try {
            $super = auth()->user();
            $super->update(['password' => request()->only('password')]);

            return response()->json(true, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
