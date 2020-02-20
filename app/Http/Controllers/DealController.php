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
            'customer' => ['json', new KeysMatchFields(new Customer())],
            'trade'    => ['json', new KeysMatchFields(new Trade())],
            'unit'     => ['json', new KeysMatchFields(new Unit())],
        ]);

        try {
            return response()->json(Deal::canGetAll()->filter()->orderBy('created_at', 'desc')->paginate(20), 201);
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

    // payment_schedule is an array of rate, payment_options and show_accessories_payments_on_pdf
    // payment_schedule.payment_options example
    // [
    //      'down_payment_options' => [1000, 2000, 3000],
    //      'months'               => [18, 24, 36],
    // ]

    // sales_status field is one of Greeting, Investigation, Selection, Presentation, The WriteBack, Close, F&I, Sold
    // defined in config('sale_status')

    // customer_type json field example ['Be-back', 'Dead'] current options: Be-back  Internet Lead  Phone-up  Walk-in  Dead
    // (with json field leaves option to expand to any amount of types instead of having to create table field for each type)

    // accessories is array of arrays with fillable fields as per Accessories::class
    // units is array of arrays with fillable fields as per Unit::class
    // trades is array of arrays with fillable fields as per Trade::class
    // purchase_information and finance_insurance are arrays of fillable fields in PurchaseInformation::class and FinanceInsurance::class
    // purchase_information belongs to a Unit, so should be submitted in the units array [..., 'units' => [..., purchase_information => [...]]]

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

            return response()->json($deal->load('customer', 'accessories', 'units.purchase_information', 'trades',
                'payment_schedule',
                'finance_insurance'), 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function update(Deal $deal)
    {
        $this->validateUpdate($deal);

        try {
            if (!isAdmin() && $deal->user_id != auth()->id()) {
                throw new \Exception('Only allowed to update own deals.');
            }

            $user = auth()->user();

            if (isAdmin() && request()->get('user_id') != auth()->id()) {
                $user = User::find(request()->get('user_id'));
            }

            $customer = request()->has('customer.id')
                ? Customer::find(request()->get('customer')['id'])
                : $user->customers()->create(request()->get('customer'));

            if ($customer->user_id != $deal->user_id) {
                throw new \Exception('Customer must belong to user');
            }

            $customer->update(request()->get('customer'));
            $deal->update(request()->all());
            $deal->updateRelatedModules();

            return response()->json($deal->load('customer', 'accessories', 'units.purchase_information', 'trades',
                'payment_schedule',
                'finance_insurance'), 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function show(Deal $deal)
    {
        try {
            if (!isAdmin() && !auth()->user()->isSuperAdmin() && $deal->user_id != auth()->id()) {
                throw new \Exception('Can only view own deals.');
            }

            return response()->json($deal, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function delete(Deal $deal)
    {
        try {
            if (!isAdmin() && $deal->user_id != auth()->id()) {
                throw new \Exception('Can only delete own deals.');
            }

            return response()->json($deal->delete(), 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    private function validateStore()
    {
        // adding validation rules to controller instead of Request for ease of access
        // and not having to rely on Request injection

        request()->validate([
            'user_id'                                                   => [
                'required',
                'exists:tenant.users,id',
                function ($attribute, $value, $fail) {
                    if (!isAdmin() && $value != auth()->id()) {
                        $fail('User not allowed to create deal for other user');
                    }
                }
            ],
            'customer'                                                  => ['required', 'array'],
            'customer.id'                                               => ['exists:tenant.customers,id'],
            'customer.first_name'                                       => ['required_without:customer.id'],
            'customer.last_name'                                        => ['required_without:customer.id'],
            'customer.phone'                                            => ['required_without:customer.id'],
            'customer.email'                                            => ['required_without:customer.id', 'email'],
            'accessories'                                               => ['nullable', 'array'],
            // Array of one or more accessories. May be submitted as empty value and will then be ignored
            'accessories.*.item_name'                                   => ['nullable', 'string'],
            'accessories.*.msrp'                                        => ['nullable', 'numeric', 'min:0'],
            'accessories.*.labor'                                       => ['nullable', 'numeric', 'min:0'],
            'accessories.*.unit_price'                                  => ['nullable', 'numeric', 'min:0'],
            'accessories.*.quantity'                                    => ['required', 'integer', 'min:1'],
            'units'                                                     => ['nullable', 'array'],
            // Array of one or more units. May be submitted as empty value and will then be ignored
            'units.*.odometer'                                          => ['nullable', 'numeric', 'min:0'],
            'units.*.year'                                              => ['nullable', 'integer'],
            'trades'                                                    => ['nullable', 'array'],
            // Array of one or more trades. May be submitted as empty value and will then be ignored
            'trades.*.odometer'                                         => ['nullable', 'numeric', 'min:0'],
            'trades.*.year'                                             => ['nullable', 'integer'],
            'sales_status'                                              => [
                'required',
                Rule::in(
                    config('sale_status')
                )
            ],
            'customer_type'                                             => [
                'bail',
                'array',
                function ($attribute, $value, $fail) {
                    if (count(array_diff($value, config('customer_types'))) > 0) {
                        $fail('Customer types must match any of ' . join(',', config('customer_types')));
                    }
                }
            ],
            'payment_schedule'                                          => ['array'],
            'payment_schedule.rate'                                     => [
                'required_with:payment_schedule',
                'numeric'
            ],
            'payment_schedule.payment_options'                          => ['array'],
            'payment_schedule.payment_options.down_payment_options'     => ['array'],
            'payment_schedule.payment_options.down_payment_options.*'   => ['numeric'],
            'payment_schedule.payment_options.months'                   => ['array'],
            'payment_schedule.payment_options.months.*'                 => [
                'numeric',
                Rule::in(config('payment_months'))
            ],
            'finance_insurance'                                         => ['nullable', 'array'],
            'finance_insurance.cash_down_payment'                       => ['nullable', 'numeric', 'min:0'],
            'finance_insurance.preferred_standard_rate'                 => ['nullable', 'numeric', 'min:0'],
            'finance_insurance.preferred_standard_term'                 => ['nullable', 'numeric', 'min:0'],
            'finance_insurance.promotional_rate'                        => ['nullable', 'numeric', 'min:0'],
            'finance_insurance.promotional_term'                        => ['nullable', 'numeric', 'min:0'],
            'finance_insurance.full_protection'                         => ['nullable', 'numeric', 'min:0'],
            'finance_insurance.limited_protection'                      => ['nullable', 'numeric', 'min:0'],
            'finance_insurance.tire_wheel'                              => ['nullable', 'numeric', 'min:0'],
            'finance_insurance.gap_coverage'                            => ['nullable', 'numeric', 'min:0'],
            'finance_insurance.theft'                                   => ['nullable', 'numeric', 'min:0'],
            'finance_insurance.priority_maintenance'                    => ['nullable', 'numeric', 'min:0'],
            'finance_insurance.appearance_protection'                   => ['nullable', 'numeric', 'min:0'],
            'units.*.purchase_information'                              => ['nullable', 'array'],
            'units.*.purchase_information.msrp'                         => [
                'required_with:purchase_information',
                'numeric'
            ],
            'units.*.purchase_information.price'                        => [
                'required_with:purchase_information',
                'numeric'
            ],
            'units.*.purchase_information.manufacturer_freight'         => ['nullable', 'numeric'],
            'units.*.purchase_information.technician_setup'             => ['nullable', 'numeric'],
            'units.*.purchase_information.accessories'                  => ['nullable', 'numeric'],
            'units.*.purchase_information.accessories_labor'            => ['nullable', 'numeric'],
            'units.*.purchase_information.labor'                        => ['nullable', 'numeric'],
            'units.*.purchase_information.riders_edge_course'           => ['nullable', 'numeric'],
            'units.*.purchase_information.miscellaneous_costs'          => ['nullable', 'numeric'],
            'units.*.purchase_information.trade_in_allowance'           => ['nullable', 'numeric'],
            'units.*.purchase_information.sales_tax_rate'               => [
                'required_with:purchase_information',
                'numeric'
            ],
            'units.*.purchase_information.payoff_balance_owed'          => ['nullable', 'numeric'],
            'units.*.purchase_information.title_trip_fee'               => ['nullable', 'numeric'],
            'units.*.purchase_information.deposit'                      => ['nullable', 'numeric'],
            'units.*.purchase_information.taxable_show_msrp_on_pdf'     => ['nullable', 'boolean'],
            'units.*.purchase_information.taxable_price'                => ['nullable', 'boolean'],
            'units.*.purchase_information.taxable_manufacturer_freight' => ['nullable', 'boolean'],
            'units.*.purchase_information.taxable_technician_setup'     => ['nullable', 'boolean'],
            'units.*.purchase_information.taxable_accessories'          => ['nullable', 'boolean'],
            'units.*.purchase_information.taxable_accessories_labor'    => ['nullable', 'boolean'],
            'units.*.purchase_information.taxable_labor'                => ['nullable', 'boolean'],
            'units.*.purchase_information.taxable_riders_edge_course'   => ['nullable', 'boolean'],
            'units.*.purchase_information.taxable_miscellaneous_costs'  => ['nullable', 'boolean'],
            'units.*.purchase_information.taxable_document_fee'         => ['nullable', 'boolean'],
            'units.*.purchase_information.tax_credit_on_trade'          => ['nullable', 'boolean'],
        ]);
    }

    private function validateUpdate($deal)
    {
        // adding validation rules to controller instead of Request for ease of access
        // and not having to rely on Request injection

        request()->validate([
            'user_id'                                                   => [
                'exists:tenant.users,id',
                function ($attribute, $value, $fail) {
                    if (!isAdmin() && $value != auth()->id()) {
                        $fail('User not allowed to create deal for other user');
                    }
                }
            ],
            'customer'                                                  => ['array'],
            'customer.id'                                               => [
                'required_with:customer',
                'exists:tenant.customers,id',
            ],
            'customer.email'                                            => ['email'],
            'accessories'                                               => ['nullable', 'array'],
            // Array of one or more accessories. May be submitted as empty value and will then be ignored
            'accessories.*.item_name'                                   => ['nullable','string'],
            'accessories.*.msrp'                                        => ['nullable', 'numeric', 'min:0'],
            'accessories.*.labor'                                       => ['nullable', 'numeric', 'min:0'],
            'accessories.*.unit_price'                                  => ['nullable', 'numeric', 'min:0'],
            'accessories.*.quantity'                                    => ['integer', 'min:1'],
            'units'                                                     => ['nullable', 'array'],
            // Array of one or more units. May be submitted as empty value and will then be ignored
            'units.*.odometer'                                          => ['nullable', 'numeric', 'min:0'],
            'units.*.year'                                              => ['nullable', 'integer'],
            'trades'                                                    => ['nullable', 'array'],
            // Array of one or more trades. May be submitted as empty value and will then be ignored
            'trades.*.odometer'                                         => ['nullable', 'numeric', 'min:0'],
            'trades.*.year'                                             => ['nullable', 'integer'],
            'sales_status'                                              => [
                'required',
                Rule::in(
                    config('sale_status')
                )
            ],
            'customer_type'                                             => [
                'bail',
                'array',
                function ($attribute, $value, $fail) {
                    if (count(array_diff($value, config('customer_types'))) > 0) {
                        $fail('Customer types must match any of ' . join(',', config('customer_types')));
                    }
                }
            ],
            'payment_schedule'                                          => ['array'],
            'payment_schedule.rate'                                     => [
                'required_with:payment_schedule',
                'numeric'
            ],
            'payment_schedule.payment_options'                          => ['array'],
            'payment_schedule.payment_options.down_payment_options'     => ['array'],
            'payment_schedule.payment_options.down_payment_options.*'   => ['numeric'],
            'payment_schedule.payment_options.months'                   => ['array'],
            'payment_schedule.payment_options.months.*'                 => [
                'numeric',
                Rule::in(config('payment_months'))
            ],
            'finance_insurance'                                         => ['nullable', 'array'],
            'finance_insurance.cash_down_payment'                       => ['nullable', 'numeric', 'min:0'],
            'finance_insurance.preferred_standard_rate'                 => ['nullable', 'numeric', 'min:0'],
            'finance_insurance.preferred_standard_term'                 => ['nullable', 'numeric', 'min:0'],
            'finance_insurance.promotional_rate'                        => ['nullable', 'numeric', 'min:0'],
            'finance_insurance.promotional_term'                        => ['nullable', 'numeric', 'min:0'],
            'finance_insurance.full_protection'                         => ['nullable', 'numeric', 'min:0'],
            'finance_insurance.limited_protection'                      => ['nullable', 'numeric', 'min:0'],
            'finance_insurance.tire_wheel'                              => ['nullable', 'numeric', 'min:0'],
            'finance_insurance.gap_coverage'                            => ['nullable', 'numeric', 'min:0'],
            'finance_insurance.theft'                                   => ['nullable', 'numeric', 'min:0'],
            'finance_insurance.priority_maintenance'                    => ['nullable', 'numeric', 'min:0'],
            'finance_insurance.appearance_protection'                   => ['nullable', 'numeric', 'min:0'],
            'purchase_information'                                      => ['nullable', 'array'],
            'purchase_information.msrp'                                 => [
                'required_with:purchase_information',
                'numeric'
            ],
            'units.*.purchase_information.price'                        => [
                'required_with:purchase_information',
                'numeric'
            ],
            'units.*.purchase_information.manufacturer_freight'         => ['nullable', 'numeric'],
            'units.*.purchase_information.technician_setup'             => ['nullable', 'numeric'],
            'units.*.purchase_information.accessories'                  => ['nullable', 'numeric'],
            'units.*.purchase_information.accessories_labor'            => ['nullable', 'numeric'],
            'units.*.purchase_information.labor'                        => ['nullable', 'numeric'],
            'units.*.purchase_information.riders_edge_course'           => ['nullable', 'numeric'],
            'units.*.purchase_information.miscellaneous_costs'          => ['nullable', 'numeric'],
            'units.*.purchase_information.trade_in_allowance'           => ['nullable', 'numeric'],
            'units.*.purchase_information.sales_tax_rate'               => [
                'required_with:purchase_information',
                'numeric'
            ],
            'units.*.purchase_information.payoff_balance_owed'          => ['nullable', 'numeric'],
            'units.*.purchase_information.title_trip_fee'               => ['nullable', 'numeric'],
            'units.*.purchase_information.deposit'                      => ['nullable', 'numeric'],
            'units.*.purchase_information.taxable_show_msrp_on_pdf'     => ['nullable', 'boolean'],
            'units.*.purchase_information.taxable_price'                => ['nullable', 'boolean'],
            'units.*.purchase_information.taxable_manufacturer_freight' => ['nullable', 'boolean'],
            'units.*.purchase_information.taxable_technician_setup'     => ['nullable', 'boolean'],
            'units.*.purchase_information.taxable_accessories'          => ['nullable', 'boolean'],
            'units.*.purchase_information.taxable_accessories_labor'    => ['nullable', 'boolean'],
            'units.*.purchase_information.taxable_labor'                => ['nullable', 'boolean'],
            'units.*.purchase_information.taxable_riders_edge_course'   => ['nullable', 'boolean'],
            'units.*.purchase_information.taxable_miscellaneous_costs'  => ['nullable', 'boolean'],
            'units.*.purchase_information.taxable_document_fee'         => ['nullable', 'boolean'],
            'units.*.purchase_information.tax_credit_on_trade'          => ['nullable', 'boolean'],
        ]);
    }
}
