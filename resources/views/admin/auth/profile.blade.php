<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title></title>
    <style>
        .layui-card {
            height: calc(100vh - 80px);
            display: flex; 
            flex-direction: column;
            justify-content: center;
            align-items: center; 
        }

        .user-info {
            display: flex;
            justify-self: center;
            align-items: center;
        }

        .user-name {
            text-align: center;
            padding: 20px;
            font-size: 20px !important;
        }

        .user-home {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 12px !important;
        }
    </style>
</head>

<body>
    <div class="pear-container">

        <div class="layui-card">
            <div class="layui-card-body" style="padding: 25px;">
                <div class="text-center layui-text">
                    <div id="userInfoHead" class="user-info">
                        @if (str_starts_with($user->avatar, 'http'))
                        <img src="{{ $user->avatar }}" id="userAvatar" width="115px" height="115px" alt="">
                        @else
                        <img src="{{ config('filesystems.disks' . '.' . config('filesystems.default') . '.' . 'url') . '/' . trim($user->avatar, '/') }}" id="userAvatar" width="115px" height="115px" alt="">
                        @endif
                    </div>
                    <h2 class="user-name">{{ $user->name }}</h2>
                    <p class="user-home">
                        @foreach ($user->roles as $item)
                        <span class="layui-btn layui-btn-xs layui-bg-blue" style="margin: 10px;">{{ $item->name }}</span>
                        @endforeach
                    </p>
                </div>
            </div>
        </div>

    </div>

    <script>

    </script>
</body>

</html>