<?php

namespace App\Http\Controllers;

use App\Accessories;
use App\Deal;
use Illuminate\Http\Request;

class AccessoriesController extends Controller
{
    /**
     * @param Deal $deal
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Deal $deal)
    {
        request()->validate([
            'item_name'  => ['required', 'string'],
            'msrp'       => ['numeric', 'min:0'],
            'labor'      => ['numeric', 'min:0'],
            'unit_price' => ['numeric', 'min:0'],
            'quantity'   => ['required', 'integer', 'min:1'],
        ]);

        try {
            if (!isAdmin() && $deal->user_id != auth()->id()) {
                throw new \Exception('Can only create accessory for own deal');
            }

            // at least one field needs to have a value
            if (!collect(request()->all())->reduce(function ($carry, $a) {
                $carry = $carry || !empty($a);

                return $carry;
            }, false)) {
                throw new \Exception('No data for accessory provided');
            }

            return response()->json($deal->accessories()->create(request()->all()), 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * @param Deal        $deal
     * @param Accessories $accessories
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Deal $deal, Accessories $accessories)
    {
        request()->validate([
            'item_name'  => ['string'],
            'msrp'       => ['numeric', 'min:0'],
            'labor'      => ['numeric', 'min:0'],
            'unit_price' => ['numeric', 'min:0'],
            'quantity'   => ['integer', 'min:1'],
        ]);

        try {
            if (!isAdmin() && $deal->user_id != auth()->id()) {
                throw new \Exception('Can only update accessory for own deal');
            }

            if ($deal->id != $accessories->deal_id) {
                throw new \Exception('Accessory does not belong to the deal');
            }

            return response()->json($accessories->update(request()->all()), 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * @param Deal        $deal
     * @param Accessories $accessories
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Deal $deal, Accessories $accessories)
    {
        try {
            if (!isAdmin() && $deal->user_id != auth()->id()) {
                throw new \Exception('Can only delete accessory from own deal');
            }

            if ($deal->id != $accessories->deal_id) {
                throw new \Exception('Accessory does not belong to the deal');
            }

            return response()->json($accessories->delete(), 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
