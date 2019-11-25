<?php

namespace App\Http\Controllers;

use App\Deal;
use App\PurchaseInformation;
use Illuminate\Http\Request;

class PurchaseInformationController extends Controller
{
    /**
     * @param Deal $deal
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Deal $deal)
    {
        request()->validate([
            'msrp'                         => ['required', 'numeric'],
            'price'                        => ['required', 'numeric'],
            'manufacturer_freight'         => ['nullable', 'numeric'],
            'technician_setup'             => ['nullable', 'numeric'],
            'accessories'                  => ['nullable', 'numeric'],
            'accessories_labor'            => ['nullable', 'numeric'],
            'labor'                        => ['nullable', 'numeric'],
            'riders_edge_course'           => ['nullable', 'numeric'],
            'miscellaneous_costs'          => ['nullable', 'numeric'],
            'trade_in_allowance'           => ['nullable', 'numeric'],
            'sales_tax_rate'               => ['required', 'numeric'],
            'payoff_balance_owed'          => ['nullable', 'numeric'],
            'title_trip_fee'               => ['nullable', 'numeric'],
            'deposit'                      => ['nullable', 'numeric'],
            'taxable_show_msrp_on_pdf'     => ['nullable', 'boolean'],
            'taxable_price'                => ['nullable', 'boolean'],
            'taxable_manufacturer_freight' => ['nullable', 'boolean'],
            'taxable_technician_setup'     => ['nullable', 'boolean'],
            'taxable_accessories'          => ['nullable', 'boolean'],
            'taxable_accessories_labor'    => ['nullable', 'boolean'],
            'taxable_labor'                => ['nullable', 'boolean'],
            'taxable_riders_edge_course'   => ['nullable', 'boolean'],
            'taxable_miscellaneous_costs'  => ['nullable', 'boolean'],
            'taxable_document_fee'         => ['nullable', 'boolean'],
            'tax_credit_on_trade'          => ['nullable', 'boolean'],
        ]);

        try {
            if (!isAdmin() && $deal->user_id != auth()->id()) {
                throw new \Exception('Can only create purchase information for own deal');
            }

            if($deal->has('purchase_information')) {
                throw new \Exception('Purchase information already exists on the deal. To make changes please use update api.');
            }

            return response()->json($deal->purchase_information()->create(request()->all()), 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * @param Deal                $deal
     * @param PurchaseInformation $purchase_information
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Deal $deal, PurchaseInformation $purchase_information)
    {
        request()->validate([
            'msrp'                         => ['numeric'],
            'price'                        => ['numeric'],
            'manufacturer_freight'         => ['nullable', 'numeric'],
            'technician_setup'             => ['nullable', 'numeric'],
            'accessories'                  => ['nullable', 'numeric'],
            'accessories_labor'            => ['nullable', 'numeric'],
            'labor'                        => ['nullable', 'numeric'],
            'riders_edge_course'           => ['nullable', 'numeric'],
            'miscellaneous_costs'          => ['nullable', 'numeric'],
            'trade_in_allowance'           => ['nullable', 'numeric'],
            'sales_tax_rate'               => ['numeric'],
            'payoff_balance_owed'          => ['nullable', 'numeric'],
            'title_trip_fee'               => ['nullable', 'numeric'],
            'deposit'                      => ['nullable', 'numeric'],
            'taxable_show_msrp_on_pdf'     => ['nullable', 'boolean'],
            'taxable_price'                => ['nullable', 'boolean'],
            'taxable_manufacturer_freight' => ['nullable', 'boolean'],
            'taxable_technician_setup'     => ['nullable', 'boolean'],
            'taxable_accessories'          => ['nullable', 'boolean'],
            'taxable_accessories_labor'    => ['nullable', 'boolean'],
            'taxable_labor'                => ['nullable', 'boolean'],
            'taxable_riders_edge_course'   => ['nullable', 'boolean'],
            'taxable_miscellaneous_costs'  => ['nullable', 'boolean'],
            'taxable_document_fee'         => ['nullable', 'boolean'],
            'tax_credit_on_trade'          => ['nullable', 'boolean'],
        ]);

        try {
            if (!isAdmin() && $deal->user_id != auth()->id()) {
                throw new \Exception('Can only update purchase information for own deal');
            }

            if ($deal->id != $purchase_information->deal_id) {
                throw new \Exception('Purchase information does not belong to the deal');
            }

            return response()->json($purchase_information->update(request()->all()), 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * @param Deal                $deal
     * @param PurchaseInformation $purchase_information
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Deal $deal, PurchaseInformation $purchase_information)
    {
        try {
            if (!isAdmin() && $deal->user_id != auth()->id()) {
                throw new \Exception('Can only delete purchase information from own deal');
            }

            if ($deal->id != $purchase_information->deal_id) {
                throw new \Exception('Purchase information does not belong to the deal');
            }

            return response()->json($purchase_information->delete(), 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
