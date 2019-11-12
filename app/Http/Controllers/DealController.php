<?php

namespace App\Http\Controllers;


use App\Customer;
use App\Deal;
use App\Rules\KeysMatchFields;
use App\Trade;
use App\Unit;
use App\User;
use Illuminate\Validation\Rule;

class DealController extends Controller
{
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

    // customer is array of new customer data or existing customer.
    // existing customer just id is sufficient: 'customer' => ['id' => CUSTOMER_ID]
    // new customer DO NOT include id: 'customer' => ['first_name' => 'First',
    //            'last_name'  => 'Last',
    //            'phone'      => '999-999-9999',
    //            'email'      => 'first@last.com',
    // ]

    // payment_options json field example
    // [
    //      'down_payment_options' => [1000, 2000, 3000],
    //      'months'               => [18, 24, 36],
    // ]

    // sales_status field is one of Greeting, Investigation, Selection, Presentation, The WriteBack, Close, F&I, Sold
    // defined in config('sale_status')

    // customer_type json field example ['Be-back', 'Dead'] current options: Be-back  Internet Lead  Phone-up  Walk-in  Dead
    // (with json field leaves option to expand to any amount of types instead of having to create table field for each type)

    public function store()
    {
        $this->validateStore();

        try {
            $user = auth()->user();

            if (isAdmin() && request()->get('user_id') != auth()->id()) {
                $user = User::find(request()->get('user_id'));
            }

            $customer = request()->has('customer.id')
                ? Customer::find(request()->get('customer')['id'])
                : $user->customers()->create(request()->get('customer'));

            if ($customer->user_id != request()->get('user_id')) {
                throw new \Exception('Customer must belong to user');
            }

            $deal = $user->deals()->create(array_merge(request()->all(), ['customer_id' => $customer->id]));

            $deal->addRelatedModules();

            return response()->json(true, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    private function validateStore()
    {
        // adding validation rules to controller instead of Request for ease of access
        // and not having to rely on Request injection

        request()->validate([
            'user_id'                                => [
                'required',
                'exists:tenant.users,id',
                function ($attribute, $value, $fail) {
                    if (!isAdmin() && $value != auth()->id()) {
                        $fail('User not allowed to create deal for other user');
                    }
                }
            ],
            'customer'                               => ['required', 'array'],
            'customer.id'                            => ['exists:tenant.customers,id'],
            'customer.first_name'                    => [empty(request()->get('customer')['id']) ? 'required' : ''],
            'customer.last_name'                     => [empty(request()->get('customer')['id']) ? 'required' : ''],
            'customer.phone'                         => [empty(request()->get('customer')['id']) ? 'required' : ''],
            'customer.email'                         => [
                empty(request()->get('customer')['id']) ? 'required' : '',
                'email'
            ],
            'sales_status'                           => [
                'required',
                Rule::in(
                    config('sale_status')
                )
            ],
            'customer_type'                          => [
                'array',
                function ($attribute, $value, $fail) {
                    if (count(array_diff($value, config('customer_types'))) > 0) {
                        $fail('Customer types must match any of ' . join(',', config('customer_types')));
                    }
                }
            ],
            'payment_options'                        => ['array'],
            'payment_options.down_payment_options'   => ['array'],
            'payment_options.down_payment_options.*' => ['numeric'],
            'payment_options.months'                 => ['array'],
            'payment_options.months.*'               => ['numeric', Rule::in(config('payment_months'))],
        ]);
    }
}
