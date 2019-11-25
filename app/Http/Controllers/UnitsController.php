<?php

namespace App\Http\Controllers;

use App\Deal;
use App\Unit;
use Illuminate\Http\Request;

class UnitsController extends Controller
{
    /**
     * @param Deal $deal
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Deal $deal)
    {
        request()->validate([
            'odometer' => ['numeric', 'min:0'],
            'year'     => ['integer'],
        ]);

        try {
            if (!isAdmin() && $deal->user_id != auth()->id()) {
                throw new \Exception('Can only create unit for own deal');
            }

            // at least one field needs to have a value
            if (!collect(request()->all())->reduce(function ($carry, $a) {
                $carry = $carry || !empty($a);

                return $carry;
            }, false)) {
                throw new \Exception('No data for unit provided');
            }

            return response()->json($deal->units()->create(request()->all()), 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function update(Deal $deal, Unit $unit)
    {
        request()->validate([
            'odometer' => ['numeric', 'min:0'],
            'year'     => ['integer'],
        ]);

        try {
            if (!isAdmin() && $deal->user_id != auth()->id()) {
                throw new \Exception('Can only update unit for own deal');
            }

            if ($deal->id != $unit->deal_id) {
                throw new \Exception('Unit does not belong to the deal');
            }

            return response()->json($unit->update(request()->all()), 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function delete(Deal $deal, Unit $unit)
    {
        try {
            if (!isAdmin() && $deal->user_id != auth()->id()) {
                throw new \Exception('Can only delete unit from own deal');
            }

            if ($deal->id != $unit->deal_id) {
                throw new \Exception('Unit does not belong to the deal');
            }

            return response()->json($unit->delete(), 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
