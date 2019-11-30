<?php

namespace App\Http\Controllers;

use App\Deal;
use App\MotoMenus\ImageToBase64;
use Carbon\Carbon;
use PDF;
use Illuminate\Http\Request;

class DownloadPdfController extends Controller
{
    public function create(Deal $deal)
    {
        request()->validate([
            'type' => ['required', 'in:deal,finance'],
            'unit' => ['required_if:type,finance'], // can be unit id or 'total' for all units
        ]);

        try {
            $deal->load('customer', 'units.purchase_information', 'trades', 'accessories', 'payment_schedule',
                'finance_insurance');
            $image = new ImageToBase64('logo.png');

            $pdf = PDF::loadView('pdf.' . request()->get('type'), compact('deal', 'image'));

            // 'deal-' . $deal->customer->name . Carbon::now()->format('m-d-Y') .'.pdf'
            return response()->json(base64_encode($pdf->output()), 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
