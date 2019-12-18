<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>The Writeback</title>

    <!-- Styles -->
    <style>
        @page {
            margin: 1cm;
        }

        html {
            margin: 1cm;
        }

        body {
            background-color: #fff;
            color: #131313;
            font-family: 'Nunito', sans-serif;
            font-weight: 200;
            margin: 0;
        }

        footer {
            position: fixed;
            bottom: -1cm;
            left: 0;
            right: 0;
            height: 1cm;
            text-align: center;
            line-height: .8cm;
            font-size: 11px;
        }

        .logo, .info {
            width: 33%;
            float: left;
        }

        .logo img {
            width: 100%;
            height: auto;
        }

        .row {
            width: 100%;
            padding: 0;
            margin: 0;
            clear: both;
            margin-bottom: 15px;
        }

        .info {
            padding-left: 20px;
        }

        .text-left {
            text-align: left;
            width: 35%;
        }

        table {
            font-size: 11px;
        }

        .info h5, .border-bottom h5, .units h5 {
            padding: 0 !important;
            margin: 0 !important;
            font-size: 12px;
        }

        .border-bottom {
            border-bottom: 1px solid #777777;
        }

        .trades {
            min-width: 70%;
        }

        .trades td {
            width: 16.6%;
        }

        .units {
            width: 100%;
            page-break-inside: avoid;
            break-inside: avoid;
        }

        .units .layout {
            width: 33.33%;
            vertical-align: top;
            padding: 0;
            margin: 0;
        }

        .layout > table {
            width: 100%;
            margin: 0;
        }

        .layout > table td {
            width: 50%;
        }

        .forty {
            width: 40% !important;
        }

        .twenty {
            width: 20% !important;
        }

        .ten {
            width: 10%;
        }

        tr:first-child .twenty, tr:first-child .forty {
            text-decoration: underline;
        }

        .will-pay {
            margin: 3px;
            padding: 5px;
            border: 1px solid #777;
        }

        .accessories {
            width: 100%;
        }

        .accessories tr:first-child > td {
            border-bottom: 1px solid #777;
            text-decoration: unset;
        }
    </style>
</head>
<body>
<footer><em>&copy; {{ date('Y') }} Tommy Ady Inc.</em><span
        style="position: absolute; right: 0;">{{ date('F j, Y')}}</span></footer>
<main>
    <div class="row" style="text-align: center;">
        MANDATORY DISCLOSURE
    </div>
    <div class="row">
        <div class="logo">
            <img src="{{ $image->base64() }}"/>
        </div>
        <div class="info">
            <h5>Sales Consultant</h5>
            <table>
                <tr>
                    <td>{{$deal->created_at->format('m/d/Y')}}</td>
                </tr>
                <tr>
                    <td>{{$deal->user->name}}</td>
                </tr>
            </table>
        </div>
    </div>
    <div class="row border-bottom">
        <h5 style="text-align: center;">Customer Information</h5>
        <table>
            <tr>
                <td class="text-left">Name:</td>
                <td>{{$deal->customer->name}}</td>
                <td class="text-left">Email:</td>
                <td>{{$deal->customer->email}}</td>
            </tr>
            <tr>
                <td class="text-left">Address:</td>
                <td>{{$deal->customer->address}}</td>
                <td class="text-left">Cell Phone:</td>
                <td>{{$deal->customer->phone}}</td>
            </tr>
            <tr>
                <td class="text-left">City, State Zip:</td>
                <td>{{$deal->customer->city_state}}</td>
                <td class="text-left">Phone #2:</td>
                <td>{{$deal->customer->phone2}}</td>
            </tr>
        </table>
    </div>

    @foreach($deal->trades as $trade)
        <div class="row border-bottom">
            <h5>Trade Information #{{$loop->iteration}}</h5>
            <table class="trades">
                <tr>
                    <td>Year:</td>
                    <td>{{$trade->year}}</td>
                    <td>Model Number:</td>
                    <td>{{$trade->model}}</td>
                    <td>Odometer:</td>
                    <td>{{$trade->odometer}}</td>
                </tr>
                <tr>
                    <td>Make:</td>
                    <td>{{$trade->make}}</td>
                    <td>VIN:</td>
                    <td>{{$trade->vin}}</td>
                    <td>Book Value:</td>
                    <td>${{$trade->book_value}}</td>
                </tr>
                <tr>
                    <td>Model:</td>
                    <td>{{$trade->model}}</td>
                    <td>Color:</td>
                    <td>{{$trade->color}}</td>
                    <td>Trade-in Value</td>
                    <td>${{$trade->trade_in_value}}</td>
                </tr>
            </table>
        </div>
    @endforeach
    @foreach($deal->units as $unit)
        <div class="row border-bottom">
            <table class="units">
                <tr>
                    <td class="layout"><h5>New Unit</h5></td>
                    <td class="layout"><h5>Purchase Information Unit #{{$loop->iteration}}</h5></td>
                    <td class="layout"><h5>Finance Options</h5></td>
                </tr>
                <tr>
                    <td class="layout">
                        <table>
                            <tr>
                                <td>Stock Number:</td>
                                <td>{{$unit->stock_number}}</td>
                            </tr>
                            <tr>
                                <td>Year:</td>
                                <td>{{$unit->year}}</td>
                            </tr>
                            <tr>
                                <td>Make:</td>
                                <td>{{$unit->year}}</td>
                            </tr>
                            <tr>
                                <td>Model:</td>
                                <td>{{$unit->model}}</td>
                            </tr>
                            <tr>
                                <td>Model Number:</td>
                                <td>{{$unit->model_number}}</td>
                            </tr>
                            <tr>
                                <td>Color:</td>
                                <td>{{$unit->color}}</td>
                            </tr>
                            <tr>
                                <td>Odometer:</td>
                                <td>{{$unit->odometer}}</td>
                            </tr>
                        </table>
                    </td>
                    <td class="layout">
                        <table>
                            @if($unit->purchase_information->taxable_show_msrp_on_pdf)
                                <tr>
                                    <td>MSRP:</td>
                                    <td>${{number_format($unit->purchase_information->msrp, 2)}}</td>
                                </tr>
                            @endif
                            <tr>
                                <td>Price:</td>
                                <td>${{number_format($unit->purchase_information->price, 2)}}</td>
                            </tr>
                            <tr>
                                <td>Manufacturer Freight:</td>
                                <td>${{number_format($unit->purchase_information->manufacturer_freight, 2)}}</td>
                            </tr>
                            <tr>
                                <td>Tech Setup & Prep:</td>
                                <td>${{number_format($unit->purchase_information->technician_setup, 2)}}</td>
                            </tr>
                            <tr>
                                <td>Accessories:</td>
                                <td>${{number_format($unit->purchase_information->accessories, 2)}}</td>
                            </tr>
                            <tr>
                                <td>Accessories Labor:</td>
                                <td>${{number_format($unit->purchase_information->accessories_labor, 2)}}</td>
                            </tr>
                            <tr>
                                <td>Labor:</td>
                                <td>${{number_format($unit->purchase_information->labor, 2)}}</td>
                            </tr>
                            <tr>
                                <td>Rider's Edge Course:</td>
                                <td>${{number_format($unit->purchase_information->riders_edge_course, 2)}}</td>
                            </tr>
                            <tr>
                                <td>Miscellaneous Costs:</td>
                                <td>${{number_format($unit->purchase_information->miscellaneous_costs, 2)}}</td>
                            </tr>
                            <tr>
                                <td>Document Fee:</td>
                                <td>${{number_format($unit->purchase_information->document_fee, 2)}}</td>
                            </tr>
                            <tr>
                                <td>Trade-in Allowance:</td>
                                <td>${{number_format($unit->purchase_information->trade_in_allowance, 2)}}</td>
                            </tr>
                            <tr>
                                <td>Sub-total:</td>
                                <td>${{number_format($unit->purchase_information->sub_total, 2)}}</td>
                            </tr>
                            <tr>
                                <td>Payoff on Trade-in:</td>
                                <td>${{number_format($unit->purchase_information->payoff_balance_owed, 2)}}</td>
                            </tr>
                            <tr>
                                <td>Trade Equity:</td>
                                <td>${{number_format($unit->purchase_information->trade_equity, 2)}}</td>
                            </tr>
                            <tr>
                                <td>Sales Tax</td>
                                <td>${{number_format($unit->purchase_information->sales_tax, 2)}}</td>
                            </tr>
                            <tr>
                                <td>Title/Trip Fee:</td>
                                <td>${{number_format($unit->purchase_information->title_trip_fee, 2)}}</td>
                            </tr>
                            <tr>
                                <td>Deposit:</td>
                                <td>${{number_format($unit->purchase_information->deposit, 2)}}</td>
                            </tr>
                            <tr>
                                <td>Cash Balance</td>
                                <td>${{number_format($unit->purchase_information->cash_balance, 2)}}</td>
                            </tr>
                        </table>
                    </td>
                    <td class="layout">
                        <table>
                            <tr>
                                <td class="forty"></td>
                                <td class="twenty">
                                    ${{number_format($deal->payment_schedule->payment_options['down_payment_options'][0], 0)}}</td>
                                <td class="twenty">
                                    ${{number_format($deal->payment_schedule->payment_options['down_payment_options'][1], 0)}}</td>
                                <td class="twenty">
                                    ${{number_format($deal->payment_schedule->payment_options['down_payment_options'][2], 0)}}</td>
                            </tr>
                            @foreach($unit->purchase_information->FinanceOptions($deal->payment_schedule) as $key => $option)
                                <tr>
                                    <td class="forty">{{ $key }} months</td>
                                    <td class="twenty">
                                        ${{number_format($option[$deal->payment_schedule->payment_options['down_payment_options'][0]], 0)}}</td>
                                    <td class="twenty">
                                        ${{number_format($option[$deal->payment_schedule->payment_options['down_payment_options'][1]], 0)}}</td>
                                    <td class="twenty">
                                        ${{number_format($option[$deal->payment_schedule->payment_options['down_payment_options'][2]], 0)}}</td>
                                </tr>
                            @endforeach
                        </table>
                        <div class="will-pay">
                            @include('pdf.__will_pay')
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    @endforeach
    @if($deal->accessories->count())
        <div class="row border-bottom">
            <table class="accessories">
                <tr>
                    <td class="forty">Item Name</td>
                    <td class="twenty">Part#</td>
                    <td class="ten">Qty</td>
                    <td>Price</td>
                    <td>Labor</td>
                    <td>Total</td>
                </tr>
                @foreach($deal->accessories as $a)
                    <tr>
                        <td>{{$a->item_name}}</td>
                        <td>{{$a->part_number}}</td>
                        <td>{{$a->quantity}}</td>
                        <td>${{number_format($a->unit_price, 2)}}</td>
                        <td>${{number_format($a->labor, 2)}}</td>
                        <td>${{number_format(($a->unit_price * $a->quantity) + $a->labor, 2)}}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="6"></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td colspan="2" style="text-align: right;">Accessories Total:</td>
                    <td>${{number_format($deal->accessories->sum(function($a) {
    return ($a->unit_price * $a->quantity) + $a->labor;
}), 2)}}</td>
                </tr>
                @if($deal->payment_schedule->show_accessories_payments_on_pdf)
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td colspan="2">
                            <table style="border: 1px solid #131313; float: right; width: 100%;">
                                <tr>
                                    <td style="border-bottom: #131313 1px solid;">Months</td>
                                    <td style="border-bottom: #131313 1px solid;">Payments</td>
                                </tr>
                                @foreach(\App\Accessories::monthlyPayments($deal->accessories->sum(function($a) {
    return ($a->unit_price * $a->quantity) + $a->labor;
}), $deal->payment_schedule->rate, $deal->payment_schedule->payment_options['months']) as $months => $payment)
                                    <tr>
                                        <td>{{$months}}</td>
                                        <td>${{number_format($payment[0], 2)}}</td>
                                    </tr>
                                @endforeach
                            </table>
                            <br style="clear: both;">
                        </td>
                    </tr>
                @endif
            </table>
        </div>
    @endif
    @if($deal->units->count() > 1)
        <div class="row">
            <table class="units">
                <tr>
                    <td class="layout"></td>
                    <td class="layout">
                        <h5>Total Cash Balance: ${{ number_format($deal->total_cash_balance, 2) }}</h5>
                    </td>
                    <td class="layout">
                        <h5>Finance Options</h5>
                        <table>
                            <tr>
                                <td class="forty"></td>
                                <td class="twenty">
                                    ${{number_format($deal->payment_schedule->payment_options['down_payment_options'][0], 0)}}</td>
                                <td class="twenty">
                                    ${{number_format($deal->payment_schedule->payment_options['down_payment_options'][1], 0)}}</td>
                                <td class="twenty">
                                    ${{number_format($deal->payment_schedule->payment_options['down_payment_options'][2], 0)}}</td>
                            </tr>
                            @foreach($deal->totalFinanceOptions($deal->payment_schedule) as $key => $option)
                                <tr>
                                    <td class="forty">{{ $key }} months</td>
                                    <td class="twenty">
                                        ${{number_format($option[$deal->payment_schedule->payment_options['down_payment_options'][0]], 0)}}</td>
                                    <td class="twenty">
                                        ${{number_format($option[$deal->payment_schedule->payment_options['down_payment_options'][1]], 0)}}</td>
                                    <td class="twenty">
                                        ${{number_format($option[$deal->payment_schedule->payment_options['down_payment_options'][2]], 0)}}</td>
                                </tr>
                            @endforeach
                        </table>
                        <div class="will-pay">
                            @include('pdf.__will_pay')
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    @endif
</main>
</body>
</html>
