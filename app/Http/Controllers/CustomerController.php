<?php

namespace App\Http\Controllers;

use App\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        try {
            return response()->json(Customer::orderBy('last_name')->paginate(20), 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function store()
    {
        request()->validate([
            'first_name' => ['required'],
            'last_name'  => ['required'],
            'phone'      => ['required'],
            'email'      => ['required', 'email'],
        ]);

        try {
            return response()->json(Customer::create(request()->all()), 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function update(Customer $customer)
    {
        request()->validate([
            'first_name' => ['string', 'min:1'],
            'last_name'  => ['string', 'min:1'],
            'phone'      => ['string'],
            'email'      => ['email'],
        ]);

        try {
            return response()->json(tap($customer)->update(request()->all()), 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function show(Customer $customer)
    {
        try {
            return response()->json($customer, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
