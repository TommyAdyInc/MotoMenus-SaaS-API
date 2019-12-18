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
            'rows'              => ['required', 'array'],
            'rows.*.id'         => ['required', 'integer'],
            'rows.*.msrp'       => ['numeric'],
            'rows.*.discount'   => ['numeric'],
            'columns'           => ['array'],
            'columns.*.enabled' => ['boolean'],
        ]);

        try {
            // only column enabled, msrp and discount can be updated
            collect(request()->get('rows'))->each(function ($row) {
                CashSpecialRow::whereId($row['id'])->update($row);
            });

            collect(request()->get('columns'))->each(function ($column) {
                CashSpecialColumn::whereId($column['id'])->update(['enabled' => $column['enabled']]);
            });

            return response()->json(true, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
