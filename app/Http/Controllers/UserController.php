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
            $user->user_role()->create(request()->all());

            return response()->json($user->load('user_role'), 201);
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
            $user->update(request()->all());
            $user->user_role()->update(['role' => request()->get('role')]);

            return response()->json($user->load('user_role'), 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function show(User $user)
    {
        try {
            return response()->json($user->load('user_role'), 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
