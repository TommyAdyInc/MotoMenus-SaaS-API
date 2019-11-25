<?php

namespace App\Http\Controllers;

use App\Deal;
use App\Trade;
use Illuminate\Http\Request;

class TradesController extends Controller
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
                throw new \Exception('Can only create trade for own deal');
            }

            // at least one field needs to have a value
            if (!collect(request()->all())->reduce(function ($carry, $a) {
                $carry = $carry || !empty($a);

                return $carry;
            }, false)) {
                throw new \Exception('No data for trade provided');
            }

            return response()->json($deal->trades()->create(request()->all()), 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * @param Deal  $deal
     * @param Trade $trade
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Deal $deal, Trade $trade)
    {
        request()->validate([
            'odometer' => ['numeric', 'min:0'],
            'year'     => ['integer'],
        ]);

        try {
            if (!isAdmin() && $deal->user_id != auth()->id()) {
                throw new \Exception('Can only update trade for own deal');
            }

            if ($deal->id != $trade->deal_id) {
                throw new \Exception('Trade does not belong to the deal');
            }

            return response()->json($trade->update(request()->all()), 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * @param Deal  $deal
     * @param Trade $trade
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Deal $deal, Trade $trade)
    {
        try {
            if (!isAdmin() && $deal->user_id != auth()->id()) {
                throw new \Exception('Can only delete trade from own deal');
            }

            if ($deal->id != $trade->deal_id) {
                throw new \Exception('Trade does not belong to the deal');
            }

            return response()->json($trade->delete(), 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
