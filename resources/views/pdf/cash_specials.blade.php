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

        .row {
            width: 100%;
            padding: 0;
            margin: 0;
            clear: both;
            margin-bottom: 15px;
        }

        .row > div {
            float: left;
            width: 20%;
        }

        table {
            font-size: 11px;
        }

        .border-bottom {
            border-bottom: 3px solid #777777;
        }
    </style>
</head>
<body>
<main>
    <h1 style="text-align: center;">Cash Specials</h1>
    <h3 style="text-align: center; font-weight: normal;">(OFFER EXPIRES AT END OF MONTH)</h3>
    <p>&nbsp;</p>
    <h3>For Cash Customers Only!</h3>
    <p>&nbsp;</p>

    @foreach($cash_specials as $cs)
        <h4 style="text-decoration: underline; margin-bottom: 5px;">{{ $cs->name }}</h4>
        <div class="row">
            <div>
                @if($cs->columns->first()->name)
                    <h6 style="margin-bottom: 0; margin-top: 0;">&nbsp;</h6>
                @endif
                    <table style="width: 100%;">
                        @foreach($cs->row_names as $row)
                            <tr>
                                <td>{{ $row->name }}</td>
                            </tr>
                        @endforeach
                    </table>
            </div>
            @foreach($cs->columns as $column)
                @if($column->enabled)
                    <div>
                        @if($column->name)
                            <h6 style="text-decoration: underline; text-align: center; margin-bottom: 0; margin-top: 0;">{{ $column->name }}</h6>
                        @endif
                        <table style="width: 100%;">
                            @foreach($column->rows as $row)
                                <tr>
                                    <td style="text-decoration: line-through; width: 50%; text-align: right">${{number_format($row->msrp,2)}}</td>
                                    <td style="width: 50%; text-align: right">${{number_format($row->discount,2)}}</td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                @endif
            @endforeach
            <br style="clear: both;" />
        </div>
    @endforeach

    <div class="row border-bottom"></div>
    <p>&nbsp;</p>
    <p style="font-size: 12px;">The current Extended Service Agreement discounts have been explained to me and I choose the option listed above.</p>
    <p>&nbsp;</p>
    <p style="font-size: 12px;">Signature:<span style="display: inline-block; width: 200px; border-bottom: 1px solid #777;"></span> Date: <span style="display: inline-block; width: 130px; border-bottom: 1px solid #777;"></span></p>
</main>
</body>
</html>
