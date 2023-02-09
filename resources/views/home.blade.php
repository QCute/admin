<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Home</title>
    <link href="https://cdn.bootcdn.net/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
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
            background-color: #323232;
        }

        .left > .top {
            width: 100%;
            height: 64px;
        }

        .left > .item {
            width: 100%;
            padding: 8px 0px 8px 0px;
            margin-bottom: 2%;
            flex-shrink: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #fff;
            font-size: 1.2em;
        }

        .left > .item:hover {
            background-color: rgba(255,255,255,.1);
            cursor: pointer;
        }

        .left > .item  > .icon {
            width: 20%;
            justify-content: center;
            align-items: center;
        }

        .left > .item > .text {
            width: 80%;
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
            background-color: #f7f7f7;
        }

        .right > .top {
            position: fixed;
            top: 0;
            right: 0;
            width: calc(100vw - 250px);
            height: 64px;
            background-color: #fff;
            box-shadow: 0 4px 5px 0 rgba(0, 0, 0, .14), 0 1px 10px 0 rgba(0, 0, 0, .12), 0 2px 4px -1px rgba(0, 0, 0, .2);
            z-index: 10;
        }

        .right > .content {
            width: 100%;
            flex-direction: column;
            padding: 24px 0px 48px 0px;
        }
        
        .right > .content > .header {
            width: calc(100% - 4%);
            margin: 12px 2%;
            font-size: 1.2em;
            font-weight: bold;
        }

        .right > .content > .block {
            width: 100%;
            flex-wrap: wrap;
        }

        .right > .content > .block > .card {
            width: 22.5%;
            height: 96px;
            margin: 12px 1%;
            border-radius: 4px;
            background-color: #fff;
            box-shadow: 0 4px 5px 0 rgba(0, 0, 0, .14), 0 1px 10px 0 rgba(0, 0, 0, .12), 0 2px 4px -1px rgba(0, 0, 0, .2);
        }
        
        .right > .content > .block > .card:first-child {
            width: 22.5%;
            height: 96px;
            margin: 12px 1% 12px 2%;
            border-radius: 4px;
            background-color: #fff;
            box-shadow: 0 4px 5px 0 rgba(0, 0, 0, .14), 0 1px 10px 0 rgba(0, 0, 0, .12), 0 2px 4px -1px rgba(0, 0, 0, .2);
        }

        .right > .content > .block > .card > .icon {
            width: 20%;
            height: 75%;
            justify-content: center;
            font-size: 2.5em;
        }

        .right > .content > .block > .card > .text {
            width: 80%;
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
        <!-- <div class="top"></div> -->
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
        <!-- <div class="top"></div> -->
        <div class="content">
            @foreach ($menu as $item)
            <div id="{{ $item->title }}" class="header">
                {{ $item->title }}
            </div>

            <div class="block">
                @foreach ($item->sub as $value)
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
