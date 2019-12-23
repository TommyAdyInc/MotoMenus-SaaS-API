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

        .trades, .units {
            width: 25%;
            float: left;
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

        .right {
            float: right;
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
        <div class="info right">
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
        <br style="clear: both;"/>
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

    <div class="row border-bottom">
        @foreach($deal->units as $unit)
            <div class="units">
                <h5>Unit Information</h5>
                <table style="width:100%;">
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
            </div>
        @endforeach
        @foreach($deal->trades as $trade)
            <div class="trades">
                <h5>Trade Information</h5>

                <table style="width:100%;">
                    <tr>
                        <td>VIN:</td>
                        <td>{{$trade->vin}}</td>
                    </tr>
                    <tr>
                        <td>Year:</td>
                        <td>{{$trade->year}}</td>
                    </tr>
                    <tr>
                        <td>Make:</td>
                        <td>{{$trade->make}}</td>
                    </tr>
                    <tr>
                        <td>Model:</td>
                        <td>{{$trade->model}}</td>
                    </tr>
                    <tr>
                        <td>Model Number:</td>
                        <td>{{$trade->model_number}}</td>
                    </tr>
                    <tr>
                        <td>Color:</td>
                        <td>{{$trade->color}}</td>
                    </tr>
                    <tr>
                        <td>Odometer:</td>
                        <td>{{$trade->odometer}}</td>
                    </tr>
                </table>
            </div>
        @endforeach
        <br style="clear: both; "/>
    </div>
    <div class="row">
        <table style="width:45%; float: left; margin-right: 5%;">
            <tr>
                <td>Price:</td>
                <td>${{number_format($deal->total['price'], 2)}}</td>
            </tr>
            <tr>
                <td>Manufacturer Freight:</td>
                <td>${{number_format($deal->total['manufacturer_freight'], 2)}}</td>
            </tr>
            <tr>
                <td>Tech Setup & Prep:</td>
                <td>${{number_format($deal->total['technician_setup'], 2)}}</td>
            </tr>
            <tr>
                <td>Accessories:</td>
                <td>${{number_format($deal->total['accessories'], 2)}}</td>
            </tr>
            <tr>
                <td>Accessories Labor:</td>
                <td>${{number_format($deal->total['accessories_labor'], 2)}}</td>
            </tr>
            <tr>
                <td>Labor:</td>
                <td>${{number_format($deal->total['labor'], 2)}}</td>
            </tr>
            <tr>
                <td>Rider's Edge Course:</td>
                <td>${{number_format($deal->total['riders_edge_course'], 2)}}</td>
            </tr>
            <tr>
                <td>Miscellaneous Costs:</td>
                <td>${{number_format($deal->total['miscellaneous_costs'], 2)}}</td>
            </tr>
            <tr>
                <td>Document Fee:</td>
                <td>${{number_format($deal->total['document_fee'], 2)}}</td>
            </tr>
            <tr>
                <td>Trade-in Allowance:</td>
                <td>${{number_format($deal->total['trade_in_allowance'], 2)}}</td>
            </tr>
            <tr>
                <td>Sub-total:</td>
                <td>${{number_format($deal->total['sub_total'], 2)}}</td>
            </tr>
            <tr>
                <td>Payoff on Trade-in:</td>
                <td>${{number_format($deal->total['payoff_balance_owed'], 2)}}</td>
            </tr>
            <tr>
                <td>Trade Equity:</td>
                <td>${{number_format($deal->total['trade_equity'], 2)}}</td>
            </tr>
            <tr>
                <td>Sales Tax</td>
                <td>${{number_format($deal->total['sales_tax'], 2)}}</td>
            </tr>
            <tr>
                <td>Title/Trip Fee:</td>
                <td>${{number_format($deal->total['title_trip_fee'], 2)}}</td>
            </tr>
            <tr>
                <td>Deposit:</td>
                <td>${{number_format($deal->total['deposit'], 2)}}</td>
            </tr>
            <tr>
                <td>Cash Balance</td>
                <td>${{number_format($deal->total['cash_balance'], 2)}}</td>
            </tr>

            @if($deal->finance_insurance->cash_down_payment) {
            <tr>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>Down Payment</td>
                <td>${{number_format($deal->finance_insurance->cash_down_payment, 2)}}</td>
            </tr>
            @endif
        </table>
        <div style="width:50%; float: left; font-size: 11px;">
            This is not a contract to purchase this vehicle. All payments are
            subject to financial approval of lending institution. Purchase of
            any product does not influence interest rate or credit approval.
            I hold the dealer harmless for my refusal of any products above.
            <br>
            <br>
            Sign Here <span style="display: inline-block; width: 95%; border-bottom: 1px solid #000;"></span>
        </div>
        <br style="clear: both; "/>
    </div>
    @if($deal->finance_insurance->preferred_standard_rate)
        <table
            style="width: @if($deal->finance_insurance->promotional_rate && $deal->finance_insurance->promotional_term) 75% @else 50% @endif;">
            <tr>
                <td style="width: @if($deal->finance_insurance->promotional_rate && $deal->finance_insurance->promotional_term)33% @else 50% @endif;">
                    <b>PREFERRED</b></td>
                <td><b>STANDARD</b></td>
                @if($deal->finance_insurance->promotional_rate && $deal->finance_insurance->promotional_term)
                    <td><b>PROMOTIONAL</b></td>
                @endif
            </tr>
            <tr>
                <td>{{$deal->finance_insurance->preferred_standard_rate}}%</td>
                <td>{{$deal->finance_insurance->preferred_standard_rate}}%</td>
                @if($deal->finance_insurance->promotional_rate && $deal->finance_insurance->promotional_term)
                    <td>{{$deal->finance_insurance->promotional_rate}}%</td>
                @endif
            </tr>
            <tr>
                <td>{{$deal->finance_insurance->preferred_standard_term}} months</td>
                <td>{{$deal->finance_insurance->preferred_standard_term}} months</td>
                @if($deal->finance_insurance->promotional_rate && $deal->finance_insurance->promotional_term)
                    <td>{{$deal->finance_insurance->promotional_term}} months</td>
                @endif
            </tr>
            @if($deal->finance_insurance->full_protection || $deal->finance_insurance->limited_protection)
                <tr>
                    <td>{{$deal->finance_insurance->full_protection ? 'Full Protection': ''}}</td>
                    <td>{{$deal->finance_insurance->limited_protection ? 'Limited Protection' : ''}}</td>
                    @if($deal->finance_insurance->promotional_rate && $deal->finance_insurance->promotional_term)
                        <td>{{$deal->finance_insurance->full_protection ? 'Full Protection': ''}}</td>
                    @endif
                </tr>
            @endif
            @if($deal->finance_insurance->tire_wheel)
                <tr>
                    <td>Tire & Wheel Protections</td>
                    <td>Tire & Wheel Protection</td>
                    @if($deal->finance_insurance->promotional_rate && $deal->finance_insurance->promotional_term)
                        <td>Tire & Wheel Protection</td>
                    @endif
                </tr>
            @endif
            @if($deal->finance_insurance->gap_coverage)
                <tr>
                    <td>Gap</td>
                    <td></td>
                    @if($deal->finance_insurance->promotional_rate && $deal->finance_insurance->promotional_term)
                        <td>Gap</td>
                    @endif
                </tr>
            @endif
            @if($deal->finance_insurance->theft)
                <tr>
                    <td>Theft</td>
                    <td></td>
                    @if($deal->finance_insurance->promotional_rate && $deal->finance_insurance->promotional_term)
                        <td>Theft</td>
                    @endif
                </tr>
            @endif
            @if($deal->finance_insurance->priority_maintanance)
                <tr>
                    <td>Priority Maintenance</td>
                    <td></td>
                    @if($deal->finance_insurance->promotional_rate && $deal->finance_insurance->promotional_term)
                        <td>Priority Maintenance</td>
                    @endif
                </tr>
            @endif
            @if($deal->finance_insurance->appearance_protection)
                <tr>
                    <td>Appearance Protection</td>
                    <td></td>
                    @if($deal->finance_insurance->promotional_rate && $deal->finance_insurance->promotional_term)
                        <td>Appearance Protection</td>
                    @endif
                </tr>
            @endif
            <tr>
                <td><b>Monthly</b>&nbsp;&nbsp;&nbsp;${{number_format($deal->preferred, 2)}}</td>
                <td><b>Monthly</b>&nbsp;&nbsp;&nbsp;${{number_format($deal->standard, 2)}}</td>
                @if($deal->finance_insurance->promotional_rate && $deal->finance_insurance->promotional_term)
                    <td><b>Monthly</b>&nbsp;&nbsp;&nbsp;${{number_format($deal->promotional, 2)}}</td>
                @endif
            </tr>
        </table>
    @endif
</main>
</body>
</html>
