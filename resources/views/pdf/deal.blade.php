<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>The Writeback</title>

    <!-- Styles -->
    <style>
        html, body {
            background-color: #fff;
            color: #131313;
            font-family: 'Nunito', sans-serif;
            font-weight: 200;
            margin: 0;
        }

        html {
            margin: 1cm;
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
        }

        .info {
            padding-left: 20px;
        }

        .info h5 {
            padding: 0 !important;
            margin: 0 !important;
        }
    </style>
</head>
<body>
<div class="row">
    <div class="logo">
        <img src="{{ $image->base64() }}"/>
    </div>
    <div class="info">
        <h5>Customer Information</h5>
    </div>
    <div class="info">
        <h5>Sales Consultant</h5>
    </div>
</div>
<div class="row">

</div>
</body>
</html>
