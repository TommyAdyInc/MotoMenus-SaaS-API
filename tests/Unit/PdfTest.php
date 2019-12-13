<?php

namespace Tests\Unit;

use App\User;
use Laravel\Passport\Passport;
use Tests\TestCase;
use UserSeeder;

class PdfTest extends TestCase
{
    private $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(UserSeeder::class);

        $this->user = User::find(1);
    }

    /**
     * @test
     *
     * @return void
     */
    public function creates_a_deal_pdf_successfully()
    {
        Passport::actingAs($this->user);

        $this->json('POST', '/api/deal', [
            'user_id'          => $this->user->id,
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
        ])->assertStatus(201);

        $response = $this->json('GET', '/api/pdf/1', ['type' => 'deal']);
        $response
            // ->dump()
            ->assertStatus(201);

        // check local if PDF created correctly
        // $pdf = base64_decode($response->getContent());

        // file_put_contents(storage_path('test.pdf'), $pdf);
    }
}
