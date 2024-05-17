<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <title>{{ config('admin.name') }}</title>
        <link rel="apple-touch-icon" href="/{{ config('admin.vendor.path') }}/pear-admin-site/assets/favicon.ico" type="image/x-icon">
        <link rel="shortcut icon bookmark" href="/{{ config('admin.vendor.path') }}/pear-admin-site/assets/favicon.ico" type="image/x-icon">
        <link rel="stylesheet" href="{{ admin_asset("./component/pear/css/pear.css") }}" />
        <link rel="stylesheet" href="{{ admin_asset("./admin/css/admin.dark.css") }}" />
        <link rel="stylesheet" href="{{ admin_asset("./admin/css/admin.css") }}" />
        <link rel="stylesheet" href="{{ admin_asset("./admin/css/variables.css") }}" />
        <!--
        <link rel="stylesheet" href="{{ admin_asset("./admin/css/reset.css") }}" />
        -->
        <!-- pear.config.colors -->
        <style> :root { --global-primary-color: #36b368; }</style>
        <link href="/{{ config('admin.vendor.path') }}/font-awesome/css/all.min.css" rel="stylesheet">
        <link href="/{{ config('admin.vendor.path') }}/remix-icon/fonts/remixicon.css" rel="stylesheet">
    </head>
    <!-- 结 构 代 码 -->
    <body class="layui-layout-body pear-admin">
        <!-- 布 局 框 架 -->
        <div class="layui-layout layui-layout-admin">
            <!-- 顶 部 样 式 -->
            <div class="layui-header">
                <!-- 菜 单 顶 部 -->
                <div class="layui-logo">
                    <!-- 图 标 -->
                    <img class="logo" src="{{ config('admin.logo') ?? admin_asset('admin/images/logo.png') }}" />
                    <!-- 标 题 -->
                    <span class="title">{{ config('admin.name') }}</span>
                </div>
                <!-- 顶 部 左 侧 功 能 -->
                <ul class="layui-nav layui-layout-left">
                    <li class="collapse layui-nav-item" onclick="toggle()"><a href="javascript:;" class="layui-icon layui-icon-shrink-right"></a></li>
                    <li class="refresh layui-nav-item" onclick="refresh()"><a href="javascript:;" class="layui-icon layui-icon-refresh-1" loading = 600></a></li>
                </ul>
                <!-- 多 系 统 菜 单 -->
                <div id="control" class="layui-layout-control"></div>
                <!-- 顶 部 右 侧 菜 单 -->
                <ul class="layui-nav layui-layout-right">
                    <li class="layui-nav-item layui-hide-xs layui-form" style="margin-left: 1em;">
                        <select class="channel-select" lay-search="" lay-filter="channel-select-filter">
                            @foreach ($channels as $item)
                            <option class="channel channel-{{ $item->id }}" value="{{ $item->id }}" {{ $item->id == $channel->id ? 'selected' : '' }}>{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </li>
                    <li class="layui-nav-item layui-hide-xs layui-form" style="margin-left: 1em;">
                        <select class="server-select" lay-search="" lay-filter="server-select-filter">
                            @foreach ($channels->first()->servers as $item)
                            <option class="hide server server-{{ $item->id }} channel-server-{{ $item->channel->id }}" value="{{ $item->id }}" {{ $item->id == $server->id ? 'selected' : '' }}>{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </li>
                    <li class="layui-nav-item layui-hide-xs"><a href="javascript:;" class="layui-icon layui-icon-release"></a></li>
                    <li class="layui-nav-item layui-hide-xs"><a href="javascript:;" class="layui-icon layui-icon-search menu-search"></a></li>
                    <li class="layui-nav-item layui-hide-xs message"></li>
                    <!--
                    <li class="layui-nav-item layui-hide-xs"><a href="javascript:;" class="fullScreen layui-icon layui-icon-screen-full"></a></li>
                    -->
                    <li class="layui-nav-item user">
                        <!-- 头 像 -->
                        <a class="layui-icon layui-icon-username" href="javascript:;"></a>
                        <!-- 功 能 菜 单 -->
                        <dl class="layui-nav-child">
                            <dd onclick="load(this.firstElementChild)">
                                <a menu-id="" menu-title="{{ trans('admin.profile') }}" menu-url="{{ admin_path(config('admin.auth.profile')) }}">
                                    {{ trans('admin.profile') }}
                                </a>
                            </dd>
                            <dd onclick="logout()">
                                <a href="javascript:;" class="logout">
                                    {{ trans('admin.logout') }}
                                </a>
                            </dd>
                        </dl>
                    </li>
                    <!-- 主 题 配 置 -->
                    <!--
                    <li class="layui-nav-item setting"><a href="javascript:;" class="layui-icon layui-icon-more-vertical"></a></li>
                    -->
                </ul>
            </div>
            <!-- 侧 边 区 域 -->
            <div class="layui-side layui-bg-black">
                <!-- 菜 单 顶 部 -->
                <div class="layui-logo">
                    <!-- 图 标 -->
                    <img class="logo" src="{{ config('admin.logo') ?? admin_asset('admin/images/logo.png') }}">
                    <!-- 标 题 -->
                    <span class="title"></span>
                </div>
                <!-- 菜 单 内 容 -->
                <div class="layui-side-scroll">
                    <div id="side">
                        <div style="height:100%!important;" class="pear-side-scroll layui-side-scroll dark-theme">
                            <ul lay-filter="side" class="layui-nav arrow pear-menu layui-nav-tree pear-nav-tree" lay-accordion="">
                                @foreach ($menus as $menu)
                                <li class="layui-nav-item {{ $select->id == $menu->id ? 'layui-this' : '' }} {{ $menu->children->keyBy('id')->has($select->id) ? 'layui-nav-itemed' : '' }}" @if($menu->type == 1) onclick="load(this.firstElementChild)" @endif>
                                    <a href="javascript:;" menu-id="{{ $menu->id }}" menu-type="{{ $menu->type }}" menu-title="{{ $menu->title }}" menu-url="{{ $menu->url }}">
                                        <i class="{{ $menu->icon }}"></i>
                                        <span>{{ $menu->title }}</span>
                                        @if ($menu->children->count() > 0)
                                        <i class="layui-icon layui-icon-down layui-nav-more"></i>
                                        @endif
                                    </a>
                                    @if ($menu->children->count() > 0)
                                    <dl class="layui-nav-child">
                                        @foreach ($menu->children as $children)
                                        <dd class="{{ $select->id == $children->id ? 'layui-this' : '' }}" onclick="load(this.firstElementChild)">
                                            <a href="javascript:;" menu-id="{{ $children->id }}" menu-type="{{ $children->type }}" menu-title="{{ $children->title }}" menu-url="{{ $children->url }}">
                                                <i class="{{ $children->icon }}"></i>
                                                <span>{{ $children->title }}</span>
                                            </a>
                                        </dd>
                                        @endforeach
                                    </dl>
                                    @endif
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!-- 视 图 页 面 -->
            <div class="layui-body">
                <!-- 内 容 页 面 -->
                <div id="content"></div>
            </div>
            <!-- 页脚 -->
            <div class="layui-footer layui-text"></div>
            <!-- 遮 盖 层 -->
            <!--
            <div class="pear-cover"></div>
            -->
            <!-- 加 载 动 画 -->
            <div class="loader-wrapper" style="display: none">
                <!-- 动 画 对 象 -->
                <div class="loader"></div>
            </div>
        </div>
        <!-- 移 动 端 便 捷 操 作 -->
        <div class="pear-collapsed-pe collapse" onclick="toggle()">
            <a href="javascript:;" class="layui-icon layui-icon-shrink-right"></a>
        </div>
        <!-- 依 赖 脚 本 -->
        <script src="{{ admin_asset("./component/layui/layui.js") }}"></script>
        <script src="{{ admin_asset("./component/pear/pear.js") }}"></script>
        <!-- 框 架 初 始 化 -->
        <script>
            layui.use(['jquery', 'menuSearch', 'popup', 'nprogress'], function() {
                // jquery serialize
                layui.jquery.fn.serializeObject = function() {
                    return this.serializeArray().reduce((acc, { name, value }) => {
                        acc[name] = acc[name] ? (Array.isArray(acc[name]) ? acc[name].concat([value]) : [acc[name], value]) : value;
                        return acc;
                    }, {});
                };

                window.$ = layui.jquery;
                window.popup = layui.popup;
                window.progress = layui.nprogress;
                window.form = layui.form;
                window.layer = layui.layer;

                layui.menuSearch.render({
                    elem: ".menu-search",
                    dataProvider: function () { return @json(array_values($menus->toArray())) },
                    select: (node) => {
                        if (node.type == "0") return;
                        // { id: '28', title: '', url: '', type: '1', openType: 'undefined' }
                        // @todo focus menu item
                        changePage(node.url);
                    }
                });

                // channel & server
                const channels = @json($channels);

                // select 事件
                form.on('select(channel-select-filter)', async function(data) {
                    var value = data.value; // 获得被选中的值
                    const channel = channels[value];

                    // request
                    const url = "{{ admin_path('/channel/change') }}";
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify({ _token: '{{ csrf_token() }}', channel: value, server: channel.servers[0] || 0 }),
                    });
                    const result = await response.json();

                    const html = channel.servers.map(server => {
                        return '<option value="' + server.id + '">' + server.name + '</option>';
                    });

                    document.querySelector('.server-select').innerHTML = html.join('');
                    form.render('select'); //这个很重要,没有这个新的option不会显示

                    refresh();
                });

                form.on('select(server-select-filter)', async function(data) {
                    // request
                    const url = "{{ admin_path('/server/change') }}";
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify({ _token: '{{ csrf_token() }}', server: data.value }),
                    });
                    const result = await response.json();

                    refresh();
                });

                // first load
                refresh().then(() => document.querySelector('.loader-wrapper').style.display = 'none');
            });

            // window.resize
            // header.click
            // body.click
            // menu.click
            // window.onresize = fix;
            document.body.onload = fix;
            document.body.querySelector('.layui-header').onclick = fix;
            document.body.querySelector('.layui-body').onclick = fix;

            function fix() {
                if (window.innerWidth >= 768) return;
                collapse();
            }

            // menu toggle
            function collapse() {
                document.body.classList.add('pear-mini');
                const pe = document.querySelector('.pear-collapsed-pe > a')
                pe.classList.add('layui-icon-spread-left');
                pe.classList.remove('layui-icon-shrink-right');
                // collapse this item
                const item = document.querySelector('#side .pear-menu .layui-nav-itemed');
                if(item) {
                    item.classList.remove('layui-nav-itemed');
                }

                // @todo add layui-nav-hover
            }

            function expend() {
                document.body.classList.remove('pear-mini');
                const pe = document.querySelector('.pear-collapsed-pe > a')
                pe.classList.remove('layui-icon-spread-left');
                pe.classList.add('layui-icon-shrink-right');
                // expend this menu item
                const item = document.querySelector('#side .pear-menu > .layui-this, #side .pear-menu .layui-nav-item:has(.layui-nav-child .layui-this)');
                if(item) {
                    item.classList.add('layui-nav-itemed');
                }
            }

            // menu toggle
            function toggle() {

                if(document.body.classList.contains('pear-mini')) {
                    expend();
                } else {
                    collapse();
                }
            }

            // page refresh
            function refresh() {
                return changePage(location.href);
            }

            function load(target) {
                collapse();
                const url = target.getAttribute('menu-url');
                history.pushState(null, '', url);
                return changePage(url);
            }

            function changePage(url) {
                progress.start();
                // fetch
                return fetch(url, {
                    method: 'GET',
                    headers: {
                        'Accept': 'text/html',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                }).then(async response => {
                    const text = await response.text();
                    if(response.redirected) {
                        // redirect to login
                        location.href = response.url;
                    } else if(response.ok) {
                        // normal page
                        document.querySelector('#content').innerHTML = text;
                    } else {
                        // error page
                        document.write(text);
                    }
                    progress.done();
                }).catch(error => {
                    document.write(error);
                });
            }

            async function logout() {

                const url = "{{ admin_path(config('admin.auth.logout')) }}";
                const response = await fetch(url, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });

                popup.success('{{ trans('admin.logout.success') }}', function() {
                    location.href = response.url;
                });
            }

        </script>        
    </body>
</html>