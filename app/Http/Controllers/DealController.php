<?php

namespace App\Http\Controllers;


use App\Customer;
use App\Deal;
use App\Rules\KeysMatchFields;
use App\Trade;
use App\Unit;

class DealController extends Controller
{
    // payment_options json field example
    // [
    //      'down_payment_options' => [1000, 2000, 3000],
    //      'months'               => [18, 24, 36],
    // ]

    // sales_status field is one of Greeting, Investigation, Selection, Presentation, The WriteBack, Close, F&I, Sold

    // customer_type json field example ['Be-back', 'Dead'] current options: Be-back  Internet Lead  Phone-up  Walk-in  Dead
    // (with json field leaves option to expand to any amount of types)

    public function index()
    {
        // validate any filter criteria
        request()->validate([
            'id'       => ['nullable', 'exists:tenant.deals,id'],
            'user_id'  => ['nullable', 'exists:tenant.users,id'],
            'customer' => ['array', new KeysMatchFields(new Customer())],
            'trade'    => ['array', new KeysMatchFields(new Trade())],
            'unit'     => ['array', new KeysMatchFields(new Unit())],
        ]);

        try {
            return response()->json(Deal::canGetAll()->orderBy('created_at', 'desc'), 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
