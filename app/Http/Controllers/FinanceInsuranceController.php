<?php

namespace App\Http\Controllers;

use App\Deal;
use App\FinanceInsurance;

class FinanceInsuranceController extends Controller
{
    /**
     * @param Deal $deal
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Deal $deal)
    {
        request()->validate([
            'cash_down_payment'       => ['nullable', 'numeric', 'min:0'],
            'preferred_standard_rate' => ['nullable', 'numeric', 'min:0'],
            'preferred_standard_term' => [
                'nullable',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) {
                    if (!in_array($value, config('payment_months'))) {
                        $fail($value . ' is not valid payment term.');
                    }
                }
            ],
            'promotional_rate'        => ['nullable', 'numeric', 'min:0'],
            'promotional_term'        => [
                'nullable',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) {
                    if (!in_array($value, config('payment_months'))) {
                        $fail($value . ' is not valid payment term.');
                    }
                }
            ],
            'full_protection'         => ['nullable', 'numeric', 'min:0'],
            'limited_protection'      => ['nullable', 'numeric', 'min:0'],
            'tire_wheel'              => ['nullable', 'numeric', 'min:0'],
            'gap_coverage'            => ['nullable', 'numeric', 'min:0'],
            'theft'                   => ['nullable', 'numeric', 'min:0'],
            'priority_maintenance'    => ['nullable', 'numeric', 'min:0'],
            'appearance_protection'   => ['nullable', 'numeric', 'min:0'],
        ]);

        try {
            if (!isAdmin() && $deal->user_id != auth()->id()) {
                throw new \Exception('Can only create F&I for own deal');
            }

            if ($deal->has('finance_insurance')) {
                throw new \Exception('F&I already exists on the deal. To make changes please use update api.');
            }

            return response()->json($deal->finance_insurance()->create(request()->all()), 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * @param Deal             $deal
     * @param FinanceInsurance $finance_insurance
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Deal $deal, FinanceInsurance $finance_insurance)
    {
        request()->validate([
            'cash_down_payment'       => ['nullable', 'numeric', 'min:0'],
            'preferred_standard_rate' => ['nullable', 'numeric', 'min:0'],
            'preferred_standard_term' => [
                'nullable',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) {
                    if (!in_array($value, config('payment_months'))) {
                        $fail($value . ' is not valid payment term.');
                    }
                }
            ],
            'promotional_rate'        => ['nullable', 'numeric', 'min:0'],
            'promotional_term'        => [
                'nullable',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) {
                    if (!in_array($value, config('payment_months'))) {
                        $fail($value . ' is not valid payment term.');
                    }
                }
            ],
            'full_protection'         => ['nullable', 'numeric', 'min:0'],
            'limited_protection'      => ['nullable', 'numeric', 'min:0'],
            'tire_wheel'              => ['nullable', 'numeric', 'min:0'],
            'gap_coverage'            => ['nullable', 'numeric', 'min:0'],
            'theft'                   => ['nullable', 'numeric', 'min:0'],
            'priority_maintenance'    => ['nullable', 'numeric', 'min:0'],
            'appearance_protection'   => ['nullable', 'numeric', 'min:0'],
        ]);

        try {
            if (!isAdmin() && $deal->user_id != auth()->id()) {
                throw new \Exception('Can only update F&I for own deal');
            }

            if ($deal->id != $finance_insurance->deal_id) {
                throw new \Exception('F&I does not belong to the deal');
            }

            return response()->json($finance_insurance->update(request()->all()), 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * @param Deal             $deal
     * @param FinanceInsurance $finance_insurance
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Deal $deal, FinanceInsurance $finance_insurance)
    {
        try {
            if (!isAdmin() && $deal->user_id != auth()->id()) {
                throw new \Exception('Can only delete F&I from own deal');
            }

            if ($deal->id != $finance_insurance->deal_id) {
                throw new \Exception('F&I does not belong to the deal');
            }

            return response()->json($finance_insurance->delete(), 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
