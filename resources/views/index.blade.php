<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Home</title>
    <link rel="apple-touch-icon" href="https://laravel.com/img/favicon/favicon.ico">
    <link rel="shortcut icon bookmark" href="https://laravel.com/img/favicon/favicon.ico">
    <link href="{{ vendor_asset('font-awesome/css/all.min.css') }}" rel="stylesheet">
    <style>
        html, body { margin: 0; width: 100vw; height: 100vh; display: flex; }
        body { opacity: 0; animation: fade-in 1s forwards; }
        @keyframes fade-in { 0% { opacity: 0; } 100% { opacity: 1; } }
        div { display: flex; align-items: center; }
        a { color: #1890ff; text-decoration: none; }

        .left {
            width: 250px;
            height: 100vh;
            overflow: auto;
            flex-direction: column;
        }

        .left > .top {
            width: 100%;
            height: 64px;
        }

        .left > .item {
            width: 80%;
            padding: 6px 0px 6px 0px;
            margin-bottom: 5%;
            flex-shrink: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #333;
            font-size: 0.875em;
            font-weight: 600;
            border-radius: 4px;
        }

        .left > .item:hover {
            background-color: #f3f4f6;
            color: rgb(168, 85, 247);
            cursor: pointer;
        }

        .left > .active {
            background-color: #f3f4f6;
            color: rgb(168, 85, 247);
            cursor: pointer;
        }

        .left > .item  > .icon {
            width: 15%;
            justify-content: center;
            align-items: center;
        }

        .left > .item > .text {
            width: 75%;
            height: 100%;
            display: flex;
            align-items: center;
            font-size: 1.2em;
        }

        .right {
            position: relative;
            width: calc(100vw - 250px);
            height: 100vh;
            overflow: auto;
            flex-direction: column;
            scroll-padding-top: 77px;
        }

        .right > .top {
            width: 100%;
            height: 64px;
            position: sticky;
            top: 0;
            right: 0;
            background-color: #fff;
            border-bottom: 1px solid #f3f4f6;
            z-index: 10;
        }

        .right > .top > .inner {
            width: 100%;
            height: 64px;
        }

        .right > .content {
            width: 100%;
            flex-direction: column;
        }
        
        .right > .content > .header {
            width: calc(100% - 4%);
            margin: 12px 2%;
            font-size: 1.875rem;
            line-height: 2.25rem;
            font-weight: bold;
        }

        /* .right > .content > .header:target {
            padding-top: 77px;
            margin-top: -77px;
        } */

        .right > .content > .block {
            width: calc(100% - 32px);
            flex-wrap: wrap;
            padding: 12px 0px 12px 0px;
        }

        .right > .content > .block > .card {
            width: 360px;
            height: 136px;
            margin: 12px 8px;
            border-radius: 4px;
            background-color: #fff;
            box-shadow: 0 4px 5px 0 rgba(0, 0, 0, .14), 0 1px 10px 0 rgba(0, 0, 0, .12), 0 2px 4px -1px rgba(0, 0, 0, .2);
        }

        .right > .content > .block > .card > .icon {
            width: 25%;
            height: 75%;
            justify-content: center;
            font-size: 2.5em;
        }

        .right > .content > .block > .card > .text {
            width: 75%;
            flex-direction: column;
        }

        .right > .content > .block > .card > .text > .title {
            width: 100%;
            margin: 0px 0px 4px 0px;
            font-size: 1.2em;
            font-weight: bold;
        }

        .right > .content > .block > .card > .text > .content {
            width: 100%;
            margin: 4px 0px 0px 0px;
            font-size: 0.76em;
        }

    </style>
    <script>

    </script>
</head>
<body>
    <div id='nav' class='left'>
        <div class="top"></div>
        @foreach ($menu as $item)
        <div class='item' onclick="javascript:location.href='#{{ $item->title }}'">
            <div class="icon">
                <i class="fa {{ $item->icon }}" style="color: {{ $item->color }}"></i>
            </div>
            <div class='text'>{{ $item->title }}</div>
        </div>
        @endforeach
    </div>
    <div id='pannel' class='right'>
        <div class="top">
            <div class="inner"></div>
        </div>
        <div class="content">
            @foreach ($menu as $item)
            <div id="{{ $item->title }}" class="header">
                {{ $item->title }}
            </div>

            <div class="block">
                @foreach ($item->children as $value)
                <div class="card">
                    <div class="icon">
                        <i class="fa {{ $value->icon }}" style="color: {{ $value->color }}"></i>
                    </div>
                    <div class="text">
                        
                        <div class="title">{{ $value->title }}</div>
                        @if ($value->url == "")
                        <div class="content">{{ $value->content }}</div>
                        @else
                        <div class="content"><a href="{{ $value->url }}">{{ $value->content }}</a></div>
                        @endif
                        
                    </div>
                </div>
                @endforeach
            </div>
            @endforeach

        </div>
    </div>
</body>
</html>
