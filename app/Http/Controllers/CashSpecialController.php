<?php

namespace App\Http\Controllers;

use App\CashSpecial;
use App\CashSpecialColumn;
use App\CashSpecialRow;
use Illuminate\Http\Request;

class CashSpecialController extends Controller
{
    public function index()
    {
        try {
            return response()->json(CashSpecial::with('columns.rows', 'row_names')->get(), 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function update()
    {
        request()->validate([
            'cash_specials'                             => ['required', 'array'],
            'cash_specials.*.columns.*.rows'            => ['required', 'array'],
            'cash_specials.*.columns.*.rows.*.id'       => ['required', 'integer'],
            'cash_specials.*.columns.*.rows.*.msrp'     => ['numeric'],
            'cash_specials.*.columns.*.rows.*.discount' => ['numeric'],
            'cash_specials.*.columns'                   => ['array'],
            'cash_specials.*.columns.*.enabled'         => ['boolean'],
        ]);

        try {
            // only column enabled, msrp and discount can be updated
            collect(request()->get('cash_specials'))->each(function ($cash_special) {
                collect($cash_special['columns'])->each(function ($column) {
                    CashSpecialColumn::whereId($column['id'])->update(['enabled' => $column['enabled']]);

                    collect($column['rows'])->each(function ($row) {
                        CashSpecialRow::whereId($row['id'])->update($row);
                    });
                });
            });

            return response()->json(true, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
