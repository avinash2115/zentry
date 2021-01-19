<!DOCTYPE html>
<html lang="en-US">
<head>
    <title></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <style type="text/css">
        body, table, td, a {
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }

        body {
            font-family: Verdana, serif;
            height: 100% !important;
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            background-color: #F7FAFC;
        }

        table {
            border-collapse: collapse !important;
            width: 100%;
        }

        table, td {
            border: 0;
            mso-cellspacing: 0;
            mso-table-lspace: 0;
            mso-table-rspace: 0;
        }

        img {
            height: auto;
            line-height: 100%;
            border: 0;
            outline: none;
            text-decoration: none;
            -ms-interpolation-mode: bicubic;
        }

        a[x-apple-data-detectors] {
            color: inherit !important;
            text-decoration: none !important;
            font-size: inherit !important;
            font-family: inherit !important;
            font-weight: inherit !important;
            line-height: inherit !important;
        }

        div[style*="margin: 16px 0;"] {
            margin: 0 !important;
        }

        h1 {
            margin-top: 25px;
            font-size: 28px !important;
            line-height: 41px !important;
        }

        h2 {
            margin-top: 25px;
            color: #272837;
            font-size: 20px !important;
            font-weight: normal !important;
            line-height: 24px !important;
        }

        p {
            margin-top: 25px;
            color: #6B6C7E;
            font-size: 16px;
            line-height: 24px;
        }

        ul {
            padding: 15px 0;
            margin-top: 25px;
            border-radius: 8px;
            background-color: #F7FAFC;
            list-style-type: none;
        }

        li {
            margin: 0 auto;
        }

        .logo {
            padding: 35px 0;
            text-align: center;
        }

        .content {
            display: table;
            max-width: 700px;
            margin: 0 auto;
            padding: 20px 40px;
            border-radius: 8px;
            border-collapse: separate;
            background-color: #FFFFFF;
            text-align: center;
        }

        .btn {
            display: block;
            width: 256px;
            margin: 25px auto;
            padding: 19px 0;
            border-radius: 27.5px;
            background-color: {{env('APP_COLOR')}};
            text-align: center;
            text-decoration: none;
            font-size: 16px;
            font-weight: bold;
            color: #FFFFFF !important;
            outline: none !important;
        }
    </style>
</head>
<body>
<table>
    <tr>
        <td class="logo">
            <a href="#" target="_blank">
                <img alt="Logo" src="{{$message->embedData(base64_decode(env('APP_LOGO')), 'logo.png')}}">
            </a>
        </td>
    </tr>
    <tr>
        <td class="content">
            @yield('content')
        </td>
    </tr>
</table>
</body>
</html>
