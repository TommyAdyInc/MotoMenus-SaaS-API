<?php

namespace App\Http\Controllers;

use App\CashSpecial;
use App\MotoMenus\ImageToBase64;
use Illuminate\Support\Facades\View;
use PDF;
use Illuminate\Http\Request;

class DownloadCashSpecialsPdfController extends Controller
{
    public function show()
    {
        try {
            $cash_specials = CashSpecial::with('columns.rows', 'row_names')->get();

            $pdf = PDF::loadView('pdf.cash_specials', compact('cash_specials'));
            $pdf->setPaper('letter');

            return response()->json(base64_encode($pdf->output()), 201); // $view->render()
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
