<?php

namespace App\Http\Controllers;

use App\Deal;
use App\PaymentSchedule;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PaymentScheduleController extends Controller
{
    /**
     * @param Deal $deal
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Deal $deal)
    {
        request()->validate([
            'rate'                                   => ['required', 'numeric'],
            'payment_options.down_payment_options'   => ['array'],
            'payment_options.down_payment_options.*' => ['numeric'],
            'payment_options.months'                 => ['array'],
            'payment_options.months.*'               => [
                'numeric',
                Rule::in(config('payment_months'))
            ],
        ]);

        try {
            if (!isAdmin() && $deal->user_id != auth()->id()) {
                throw new \Exception('Can only create payment schedule for own deal');
            }

            if($deal->has('payment_schedule')) {
                throw new \Exception('Payment schedule already exists on the deal. To make changes please use update api.');
            }

            return response()->json($deal->payment_schedule()->create(request()->all()), 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * @param Deal            $deal
     * @param PaymentSchedule $payment_schedule
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Deal $deal, PaymentSchedule $payment_schedule)
    {
        request()->validate([
            'rate'                                   => ['numeric'],
            'payment_options.down_payment_options'   => ['array'],
            'payment_options.down_payment_options.*' => ['numeric'],
            'payment_options.months'                 => ['array'],
            'payment_options.months.*'               => [
                'numeric',
                Rule::in(config('payment_months'))
            ],
        ]);

        try {
            if (!isAdmin() && $deal->user_id != auth()->id()) {
                throw new \Exception('Can only update payment schedule for own deal');
            }

            if ($deal->id != $payment_schedule->deal_id) {
                throw new \Exception('Payment schedule does not belong to the deal');
            }

            return response()->json($payment_schedule->update(request()->all()), 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * @param Deal            $deal
     * @param PaymentSchedule $payment_schedule
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Deal $deal, PaymentSchedule $payment_schedule)
    {
        try {
            if (!isAdmin() && $deal->user_id != auth()->id()) {
                throw new \Exception('Can only delete payment schedule from own deal');
            }

            if ($deal->id != $payment_schedule->deal_id) {
                throw new \Exception('Payment schedule does not belong to the deal');
            }

            return response()->json($payment_schedule->delete(), 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
