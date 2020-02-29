<?php

namespace App\Http\Controllers;

use App\Deal;
use App\MotoMenus\ImageToBase64;
use App\MotoMenus\PaymentsCalculation;
use PDF;
use Illuminate\Http\Request;

class DownloadPdfController extends Controller
{
    public function show(Deal $deal)
    {
        request()->validate([
            'type'  => ['required', 'in:deal,finance'],
            'unit'  => ['nullable', 'array'],
            // can be unit id or null for all units,
            'trade' => ['nullable', 'array'],
            // trade id or null for all
        ]);

        try {
            $deal->load('customer', 'units.purchase_information', 'trades', 'accessories', 'payment_schedule',
                'finance_insurance', 'user');

            // if selected unit or trade doesn't belong to deal throw error
            if (request()->get('type') == 'finance') {
                if (!empty(request()->get('unit')) && !$deal->units->whereIn('id', request()->get('unit'))) {
                    throw new \Exception('Unit doesn\'t belong to the deal');
                }

                if (!empty(request()->get('trade')) && !$deal->trades->whereIn('id', request()->get('trade'))) {
                    throw new \Exception('Trade doesn\'t belong to the deal');
                }
            }

            // if finance need to add total purchase info for multiple units
            if (request()->get('type') == 'finance') {
                $deal = $this->setTotal($deal);
            }

            $image = new ImageToBase64('logo.png');

            $pdf = PDF::loadView('pdf.' . request()->get('type'), compact('deal', 'image'));
            $pdf->setPaper('letter');

            return response()->json(base64_encode($pdf->output()), 201); // $view->render()
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getTrace()], 422);
        }
    }

    private function setTotal(Deal $deal)
    {
        // filter for unit
        if (!empty(request()->get('unit'))) {
            $deal->units = $deal->units->whereIn('id', request()->get('unit'));
        }

        // filter for trade
        if (!empty(request()->get('trade'))) {
            $deal->trades = $deal->trades->whereIn('id', request()->get('trade'));
        }

        // set total purchase information
        $total = collect([
            'price'                => 0,
            'manufacturer_freight' => 0,
            'technician_setup'     => 0,
            'accessories'          => 0,
            'accessories_labor'    => 0,
            'labor'                => 0,
            'riders_edge_course'   => 0,
            'miscellaneous_costs'  => 0,
            'document_fee'         => 0,
            'trade_in_allowance'   => 0,
            'payoff_balance_owed'  => 0,
            'title_trip_fee'       => 0,
            'deposit'              => 0,
            'sub_total'            => 0,
            'trade_equity'         => 0,
            'sales_tax'            => 0,
            'cash_balance'         => 0
        ]);

        $deal->units->each(function ($u) use (&$total) {
            collect([
                'price',
                'manufacturer_freight',
                'technician_setup',
                'accessories',
                'accessories_labor',
                'labor',
                'riders_edge_course',
                'miscellaneous_costs',
                'document_fee',
                'trade_in_allowance',
                'payoff_balance_owed',
                'title_trip_fee',
                'deposit',
            ])
                ->each(function ($field) use ($u, &$total) {
                    $total[$field] += $u->purchase_information->{$field};
                });

            $total['sub_total'] += $u->purchase_information->sub_total;
            $total['sales_tax'] += $u->purchase_information->sales_tax;
            $total['trade_equity'] += $u->purchase_information->trade_equity;
            $total['cash_balance'] += $u->purchase_information->cash_balance;
        });

        $deal->total = $total;

        $deal->preferred = $this->monthly($deal, 'preferred');
        $deal->standard = $this->monthly($deal, 'standard');
        $deal->promotional = $this->promotional($deal);

        return $deal;
    }

    private function monthly(Deal $deal, string $type)
    {
        if($deal->finance_insurance->preferred_standard_rate && $deal->finance_insurance->preferred_standard_term) {
            $monthly = new PaymentsCalculation([$deal->finance_insurance->cash_down_payment],
                $deal->finance_insurance->preferred_standard_rate,
                $deal->total['cash_balance'] + $deal->finance_insurance->{$type});

            return $monthly->getPayments()[$deal->finance_insurance->preferred_standard_term][$deal->finance_insurance->cash_down_payment];
        }

        return null;
    }

    private function promotional(Deal $deal)
    {
        if($deal->finance_insurance->promotional_rate && $deal->finance_insurance->promotional_term) {
            $monthly = new PaymentsCalculation([$deal->finance_insurance->cash_down_payment],
                $deal->finance_insurance->promotional_rate,
                $deal->total['cash_balance'] + $deal->finance_insurance->preferred);

            return $monthly->getPayments()[$deal->finance_insurance->promotional_term][$deal->finance_insurance->cash_down_payment];
        }

        return null;
    }
}
