<?php

namespace App\Http\Controllers;

use App\Customer;
use App\User;

class CustomerController extends Controller
{
    public function index()
    {
        try {
            return response()->json(Customer::filter()->canSeeAll()->orderBy('last_name')->paginate(20), 201);
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
            $user = $this->canCreateForOtherUser()
                ? User::find(request()->get('user_id'))
                : auth()->user();

            $customer = $user->customers()->create(request()->all());
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
            if(!isAdmin() && !auth()->user()->isSuperAdmin()) {
                if(auth()->id() !== $customer->user_id) {
                    throw new \Exception('Cannot view other user customer.');
                }
            }
            return response()->json($customer, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    protected function canCreateForOtherUser()
    {
        return isAdmin() && request()->has('user_id') && auth()->id() != request()->get('user_id');
    }
}
