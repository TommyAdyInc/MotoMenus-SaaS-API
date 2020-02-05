<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        try {
            $query = User::orderBy('name');

            if (request()->has('filter')) {
                if (request()->get('filter') == 'disabled') {
                    $query->onlyTrashed();
                }

                if (request()->get('filter') == 'all') {
                    $query->withTrashed();
                }
            } else {
                $query->withTrashed();
            }

            return response()->json(request()->get('no_paging') ? $query->select('id', 'name')->get() : $query->paginate(20), 201);
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
            'email'    => ['email', 'unique:tenant.users,email,' . $user->id],
            'password' => ['nullable', 'string', 'min:8'],
            'role'     => ['in:admin,user']
        ]);

        try {
            if (!isAdmin() && !auth()->user()->isSuperAdmin()) {
                if (auth()->id() !== $user->id) {
                    throw new \Exception('Can only modify own user data.');
                }

                if (request()->has('role') && request()->get('role') !== 'user') {
                    throw new \Exception('Not allowed to modify user role.');
                }
            }

            if (request()->get('password')) {
                $user->update(request()->all());
            } else {
                $user->update(request()->except('password'));
            }

            return response()->json($user, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function show(User $user)
    {
        try {
            if (!isAdmin() && !auth()->user()->isSuperAdmin()) {
                if (auth()->id() !== $user->id) {
                    throw new \Exception('Can only view own user data.');
                }
            }

            return response()->json($user, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function delete(User $user)
    {
        try {
            return response()->json($user->delete(), 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function restore($id)
    {
        try {
            $user = User::onlyTrashed()->findOrFail($id);

            $user->deleted_at = null;
            $user->save();

            return response()->json(true, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
