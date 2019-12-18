<?php

use App\User;
use Illuminate\Database\Seeder;

class CashSpecialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        collect([
            'Extended Service Plan'   => [
                'columns' => ['2 years', '3 years', '4 years', '5 years'],
                'rows'    => ['0-250cc', '251-500cc', '501-800cc', '801-1100cc', '1101+'],
            ],
            'Tire & Wheel Protection' => [
                'columns' => [],
                'rows'    => ['ATV\'s', 'SXS\'s', 'MOTORCYCLES'],
            ],
            'Gap Coverage'            => [
                'columns' => [],
                'rows'    => [],
            ],
            'Theft Protection'        => [
                'columns' => [],
                'rows'    => [],
            ]
        ])->each(function ($cs, $name) {
            $cash_special = \App\CashSpecial::create(['name' => $name]);

            if (empty($cs['columns']) && empty($cs['rows'])) {
                $column = $cash_special->columns()->create([]);
                $row = $cash_special->row_names()->create([]);

                \App\CashSpecialRow::create([
                    'cash_special_row_name_id' => $row->id,
                    'cash_special_column_id'   => $column->id
                ]);
            } elseif (empty($cs['rows'])) {
                $row = $cash_special->row_names()->create([]);

                collect($cs['columns'])->each(function ($column) use ($cash_special, $row) {
                    $column = $cash_special->columns()->create(['name' => $column]);

                    \App\CashSpecialRow::create([
                        'cash_special_row_name_id' => $row->id,
                        'cash_special_column_id'   => $column->id
                    ]);
                });
            } elseif (empty($cs['columns'])) {
                $column = $cash_special->columns()->create([]);

                collect($cs['rows'])->each(function ($row) use ($cash_special, $column) {
                    $row = $cash_special->row_names()->create(['name' => $row]);

                    \App\CashSpecialRow::create([
                        'cash_special_row_name_id' => $row->id,
                        'cash_special_column_id'   => $column->id
                    ]);
                });
            } else {
                collect($cs['rows'])->each(function ($row) use ($cash_special, $cs) {
                    $row = $cash_special->row_names()->create(['name' => $row]);

                    collect($cs['columns'])->each(function ($column) use ($cash_special, $row) {
                        $column = $cash_special->columns()->firstOrCreate(['name' => $column]);

                        \App\CashSpecialRow::create([
                            'cash_special_row_name_id' => $row->id,
                            'cash_special_column_id'   => $column->id
                        ]);
                    });
                });
            }
        });
    }
}
