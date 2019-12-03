<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        try {
            return response()->json(User::all(), 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function store()
    {
        request()->validate([
            'name'     => ['required', 'string', 'min:2'],
            'email'    => ['required', 'email', 'unique:tenant.users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role'     => ['required', 'in:admin,user']
        ]);

        try {
            $user = User::create(request()->all());

            return response()->json($user, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function update(User $user)
    {
        request()->validate([
            'name'     => ['string', 'min:2'],
            'email'    => ['email', 'unique:tenant.users,email,' . request()->get('id')],
            'password' => ['string', 'min:8'],
            'role'     => ['in:admin,user']
        ]);

        try {
            if(!isAdmin()) {
                if(auth()->id() !== $user->id) {
                    throw new \Exception('Can only modify own user data.');
                }

                if(request()->has('role') && request()->get('role') !== 'user') {
                    throw new \Exception('Not allowed to modify user role.');
                }
            }

            $user->update(request()->all());

            return response()->json($user, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function show(User $user)
    {
        try {
            if(!isAdmin()) {
                if(auth()->id() !== $user->id) {
                    throw new \Exception('Can only view own user data.');
                }
            }

            return response()->json($user, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}