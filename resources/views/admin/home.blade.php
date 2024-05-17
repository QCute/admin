<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title></title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <style>
        .layui-card-body {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .icon {
            margin-top: 2em;
        }

        .text {
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="pear-container layui-row layui-col-space15">


        <div class="layui-col-md4">
            <div class="layui-card">
                <div class="layui-card-header">{{ trans('admin.dashboard.environments') }}</div>
                <div class="layui-card-body">
                    <table class="layui-table" lay-skin="nob" lay-even>
                        <colgroup>
                            <col min-width="120" />
                            <col min-width="120" />
                        </colgroup>

                        <tbody id="environments">
                            @foreach ($environments as $row)
                            <tr><td>{{ $row['name'] }}</td><td>{{ $row['value'] }}</td></tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="layui-col-md4">
            <div class="layui-card">
                <div class="layui-card-header">{{ trans('admin.dashboard.modules') }}</div>
                <div class="layui-card-body">
                <table class="layui-table" lay-skin="nob" lay-even>
                        <colgroup>
                            <col width="120" />
                            <col width="120" />
                        </colgroup>
                
                        <tbody id="modules">
                            <tr><td class="ui ui-name">LayUI</td><td class="ui ui-version"></td></tr>
                            <tr><td class="framework framework-name">PearAdmin</td><td class="framework framework-version"></td></tr>
                        </tbody>
                        <script>
                            document.querySelector('.ui-version').innerText = layui.v;
                            document.querySelector('.framework-version').innerText = layui.cache.version;
                            layui.use(Object.keys(layui.modules), function () { 
                                Object
                                    .keys(layui.modules)
                                    .filter(module => layui[module])
                                    .filter(module => layui[module].version)
                                    .forEach(module => {
                                        const name = document.createElement('td');
                                        name.innerText = module;
                                        const version = document.createElement('td');
                                        version.innerText = layui[module].version;
                                        const tr = document.createElement('tr');
                                        tr.appendChild(name);
                                        tr.appendChild(version);
                                        document.getElementById('modules').appendChild(tr);
                                    }); 
                            });
                        </script>
                    </table>

                    <div class="icon">
                        <svg focusable="false" data-icon="smile" width="6em" height="6em" aria-hidden="true"
                            viewBox="64 64 896 896">
                            <path
                                d="M512 64C264.6 64 64 264.6 64 512s200.6 448 448 448 448-200.6 448-448S759.4 64 512 64zm0 820c-205.4 0-372-166.6-372-372s166.6-372 372-372 372 166.6 372 372-166.6 372-372 372z"
                                style="fill: var(--global-primary-color);">
                            </path>
                            <path
                                d="M512 140c-205.4 0-372 166.6-372 372s166.6 372 372 372 372-166.6 372-372-166.6-372-372-372zM288 421a48.01 48.01 0 0196 0 48.01 48.01 0 01-96 0zm224 272c-85.5 0-155.6-67.3-160-151.6a8 8 0 018-8.4h48.1c4.2 0 7.8 3.2 8.1 7.4C420 589.9 461.5 629 512 629s92.1-39.1 95.8-88.6c.3-4.2 3.9-7.4 8.1-7.4H664a8 8 0 018 8.4C667.6 625.7 597.5 693 512 693zm176-224a48.01 48.01 0 010-96 48.01 48.01 0 010 96z"
                                fill="#e6f7ff">
                            </path>
                            <path
                                d="M288 421a48 48 0 1096 0 48 48 0 10-96 0zm376 112h-48.1c-4.2 0-7.8 3.2-8.1 7.4-3.7 49.5-45.3 88.6-95.8 88.6s-92-39.1-95.8-88.6c-.3-4.2-3.9-7.4-8.1-7.4H360a8 8 0 00-8 8.4c4.4 84.3 74.5 151.6 160 151.6s155.6-67.3 160-151.6a8 8 0 00-8-8.4zm-24-112a48 48 0 1096 0 48 48 0 10-96 0z"
                                style="fill: var(--global-primary-color);">
                            </path>
                        </svg>
                    </div>
                    <div class="text">Hello World</div>
                    
                </div>
            </div>
        </div>
    
        <div class="layui-col-md4">
            <div class="layui-card">
                <div class="layui-card-header">{{ trans('admin.dashboard.dependencies') }}</div>
                <div class="layui-card-body">
                    <table class="layui-table" lay-skin="nob" lay-even>
                        <colgroup>
                            <col width="120" />
                            <col width="120" />
                        </colgroup>

                        <tbody id="dependencies">
                            @foreach ($dependencies as $name => $version)
                            <tr><td>{{ $name }}</td><td><button class="layui-btn layui-btn-xs">{{ $version }}</button></td></tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


    </div>
</body>

</html>