<?php

namespace App\Http\Controllers;

use App\Website;
use Hyn\Tenancy\Contracts\Repositories\HostnameRepository;
use Hyn\Tenancy\Contracts\Repositories\WebsiteRepository;
use Hyn\Tenancy\Models\Hostname;
use Illuminate\Http\Request;

class TenantsController extends Controller
{
    public function index()
    {
        try {
            return response()->json(Website::withTrashed()->with('hostnames')->paginate(15), 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function show(Website $website)
    {
        try {
            return response()->json($website->load('hostnames'), 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function store()
    {
        request()->validate([
            'fqdn'       => ['required', 'unique:connection_system.hostnames,fqdn'],
            'store_name' => ['required', 'min:3',]
        ]);

        try {
            $website = new Website;
            $website->store_name = request()->get('store_name');
            app(WebsiteRepository::class)->create($website);

            try {
                $hostname = new Hostname;
                $hostname->fqdn = request()->get('fqdn');
                $hostname = app(HostnameRepository::class)->create($hostname);
                app(HostnameRepository::class)->attach($hostname, $website);
            } catch (\Exception $e) {
                $website->forceDelete();

                throw $e;
            }

            return response()->json($website->fresh()->load('hostnames'), 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function update(Website $website)
    {
        request()->validate([
            'store_name' => ['required', 'min:3'],
        ]);

        try {
            $website->update(['store_name' => request()->get('name')]);

            return response()->json(true, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function delete(Website $website)
    {
        try {
            $website->delete();

            return response()->json(true, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function restore($website)
    {
        try {
            $website = Website::onlyTrashed()->find($website);
            $website->deleted_at = null;
            $website->save();

            return response()->json(true, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
