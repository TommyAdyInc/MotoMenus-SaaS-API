<?php


use App\GlobalSetting;
use App\User;
use Illuminate\Database\Seeder;

class DealsSeeder extends Seeder
{
    public function run()
    {
        $request = [
            'customer'         => [
                'first_name' => 'some_name',
                'last_name'  => 'some_name',
                'email'      => 'some_name@test.com',
                'phone'      => '2344567890'
            ],
            'sales_status'     => 'Greeting',
            'trades'           => [
                [
                    'vin'            => 'YT123456',
                    'year'           => 2018,
                    'make'           => 'yamaha',
                    'odometer'       => 3456,
                    'trade_in_value' => 3000,
                    'book_value'     => 3500
                ],
                [
                    'vin'            => 'YT987654',
                    'year'           => 2018,
                    'make'           => 'yamaha',
                    'odometer'       => 3456,
                    'trade_in_value' => 3000,
                    'book_value'     => 3500
                ]
            ],
            'units'            => [
                [
                    'stock_number'         => 'Y123456',
                    'year'                 => 2018,
                    'make'                 => 'yamaha',
                    'odometer'             => 1,
                    'purchase_information' => [
                        'msrp'           => 12345,
                        'price'          => 23456,
                        'sales_tax_rate' => 6.225,
                        'document_fee'   => 259,
                    ]
                ],
                [
                    'stock_number'         => 'Y654323',
                    'year'                 => 2018,
                    'make'                 => 'honda',
                    'odometer'             => 1,
                    'purchase_information' => [
                        'msrp'               => 23456,
                        'price'              => 24567,
                        'sales_tax_rate'     => 6.225,
                        'document_fee'       => 259,
                        'trade_in_allowance' => 3000,
                    ]
                ]
            ],
            'payment_schedule' => [
                'rate'                             => 13.49,
                'show_accessories_payments_on_pdf' => 1,
                'payment_options'                  => [
                    'down_payment_options' => [1000, 2000, 3000],
                    'months'               => [18, 24, 48],
                ]
            ],
            'accessories'      => [
                [
                    'part_number' => 'ACC12345',
                    'item_name'   => 'Item',
                    'quantity'    => 1,
                    'unit_price'  => 20,
                    'labor'       => 50,
                ],
                [
                    'part_number' => 'ACC98765',
                    'item_name'   => 'Item',
                    'quantity'    => 1,
                    'unit_price'  => 20,
                    'labor'       => 50,
                ]
            ]
        ];

        $user = User::find(1);
        \App\Unit::reguard();

        for ($i = 0; $i < 30; $i++) {
            $customer = $user->customers()->create($request['customer']);
            $deal = $user->deals()->create(['customer_id' => $customer->id, 'sales_status' => $request['sales_status']]);

            // add any Units to deal
            $document_fee = GlobalSetting::first()->document_fee;

            collect($request['units'])->each(function ($unit) use ($document_fee, $deal) {
                $u = $deal->units()->create($unit);

                // add Purchase information to unit
                if (isset($unit['purchase_information'])) {
                    $u->purchase_information()->create(array_merge(
                            collect($unit['purchase_information'])->except('document_fee')->all(),
                            ['document_fee' => $document_fee])
                    );
                }
            });

            // add any Trades to deal
            collect($request['trades'])->each(function ($trade) use ($deal) {
                $deal->trades()->create($trade);
            });

            // Add any accessories to deal
            collect($request['accessories'])->each(function ($acc) use ($deal) {
                $deal->accessories()->create($acc);
            });

            // add payment schedule to deal
            $deal->payment_schedule()->create($request['payment_schedule']);

            // add F&I to deal
            // $deal->finance_insurance()->create($request['finance_insurance']);
        }
    }
}
