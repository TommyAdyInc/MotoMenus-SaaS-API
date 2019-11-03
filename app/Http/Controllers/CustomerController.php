<?php

namespace App\Http\Controllers;

use App\Customer;

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
            $customer = auth()->user()->customers()->create(request()->all());
            $customer->note()->create(request()->only('note'));

            return response()->json($customer->load('note', 'user'), 201);
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
            'user_id'    => ['exists:tenant.users,id'],
            'note'       => ['string'],
        ]);

        try {
            if(!isAdmin()) {
                if(auth()->id() !== $customer->user_id) {
                    throw new \Exception('Cannot update other user customer.');
                }
            }

            $customer = tap($customer)->update(request()->all());
            $customer->note()->update(request()->only('note'));

            return response()->json($customer->load('note'), 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function show(Customer $customer)
    {
        try {
            if(!isAdmin()) {
                if(auth()->id() !== $customer->user_id) {
                    throw new \Exception('Cannot view other user customer.');
                }
            }
            return response()->json($customer, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
