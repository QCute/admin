<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="login">
    <title>{{ trans('admin.login') }}</title>
    <link rel="apple-touch-icon" href="{{ vendor_asset('pear-admin-site/assets/favicon.ico') }}" type="image/x-icon">
    <link rel="shortcut icon bookmark" href="{{ vendor_asset('pear-admin-site/assets/favicon.ico') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ admin_asset("component/pear/css/pear.css") }}" />
    <link rel="stylesheet" href="{{ admin_asset("admin/css/other/login.css") }}" />
    <link rel="stylesheet" href="{{ admin_asset("admin/css/variables.css") }}" />
    <link rel="stylesheet" href="{{ admin_asset("admin/css/reset.css") }}" />
    <style>
        .layui-form-checked:hover>div, .layui-form-checked>div { background-color: unset!important; }
    </style>
    <script>
        fetch('{{ admin_asset("config/pear.config.json") }}')
            .then(response => response.json())
            .then(json => {
                const color = json.colors.find(color => color.id === json.theme.defaultColor).color;
                document.documentElement.style.setProperty('--global-primary-color', color);
            });
    </script>
    <script>if (window.self != window.top) { top.location.reload(); }</script>
</head>

<body>
    <div class="login-page" style="background-image: url({{ admin_asset("admin/images/background.svg") }})">
        <div class="layui-row">
            <div class="layui-col-sm6 login-bg layui-hide-xs">
                <img class="login-bg-img" src="{{ admin_asset("admin/images/banner.png") }}" alt="" />
            </div>
            <div class="layui-col-sm6 layui-col-xs12 login-form">
                <div class="layui-form">
                    <div class="form-center">
                        <div class="form-center-box">
                            <div class="top-log-title">
                                <img class="top-log" src="{{ vendor_asset('pear-admin-site/assets/images/un28.svg')}}" alt="" />
                                <span>Pear Admin 4.0</span>
                            </div>
                            <div class="top-desc">
                                {{ config('admin.sign') }}
                            </div>
                            <form style="margin-top: 30px;" class="layui-form" action="return false;">
                                <div class="layui-form-item">
                                    <div class="layui-input-wrap">
                                        <div class="layui-input-prefix">
                                            <i class="layui-icon layui-icon-username"></i>
                                        </div>
                                        <input 
                                            name="username" 
                                            lay-verify="required" 
                                            placeholder="{{ trans('admin.login.username') }}" 
                                            autocomplete="off" 
                                            class="layui-input"
                                        />
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <div class="layui-input-wrap">
                                        <div class="layui-input-prefix">
                                            <i class="layui-icon layui-icon-password"></i>
                                        </div>
                                        <input 
                                            type="password" 
                                            name="password" 
                                            value="" 
                                            lay-verify="required|password" 
                                            placeholder="{{ trans('admin.login.password') }}" 
                                            autocomplete="off" 
                                            class="layui-input" 
                                            lay-affix="eye"
                                        />
                                    </div>
                                </div>
                                @if (config('admin.auth.captcha'))
                                <div class="layui-form-item tab-log-verification">
                                    <div class="verification-text">
                                        <div class="layui-input-wrap">
                                            <div class="layui-input-prefix">
                                                <i class="layui-icon layui-icon-auz"></i>
                                            </div>
                                            <input 
                                                type="text" 
                                                name="captcha" 
                                                lay-verify="required" 
                                                value="" 
                                                placeholder="{{ trans('admin.login.captcha') }}" 
                                                autocomplete="off" 
                                                class="layui-input"
                                            />
                                        </div>
                                    </div>
                                    <img src="{{ $captcha }}" alt="" id="captcha" class="verification-img" style="cursor: pointer;" />
                                </div>
                                @endif
                                @if (config('admin.auth.remember'))
                                <div class="layui-form-item">
                                    <div class="remember-passsword">
                                        <div class="remember-cehcked">
                                            <input 
                                                type="checkbox" 
                                                name="remember" 
                                                lay-skin="primary" 
                                                title="{{ trans('admin.login.remember') }}"
                                            />
                                        </div>
                                    </div>
                                </div>
                                @endif
                                {{ csrf_field() }}
                                <div class="login-btn">
                                    <button type="submit" lay-submit lay-filter="login" class="layui-btn login">
                                        {{ trans('admin.login') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- import assets -->
    <script src="{{ admin_asset("component/layui/layui.js") }}"></script>
    <script src="{{ admin_asset("component/pear/pear.js") }}"></script>
    <script>
        layui.use(['form', 'button', 'popup'], function () {
            const form = layui.form;
            const button = layui.button;
            const popup = layui.popup;

            document.querySelector('#captcha').addEventListener('click', async (event) => {
                const url = "{{ admin_path(config("admin.auth.captcha")) }}";
                const response = await fetch(url, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    }
                });
                const { captcha } = await response.json();
                event.target.src = captcha;
            });

            // submit
            form.on('submit(login)', async function (data) {

                // animation
                const submit = button.load({
                    elem: '.login',
                    time: Math.pow(2, 31) - 1,
                    done: function () {}
                });

                const url = "{{ admin_path(config("admin.auth.login")) }}";

                // remove empty fields
                const field = Object
                    .entries(data.field)
                    .filter(([k, v]) => v)
                    .reduce((acc, [k, v]) => {
                        acc[k] = v;
                        return acc;
                    }, {});

                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify(field),
                });

                submit.stop();

                if (!response.ok) {
                    popup.failure(`{{ trans('admin.login.failure') }}: ${response.status}: ${response.statusText}`);
                    return false;
                }

                const { code, msg, captcha } = await response.json();

                if (code) {
                    popup.failure(`{{ trans('admin.login.failure') }}: ${msg}`);
                    document.querySelector('#captcha').src = captcha;
                    return false;
                }

                popup.success('{{ trans('admin.login.success') }}', function () {
                    location.href = '{{ admin_path('/') }}';
                });

                return false;
            });
        })
    </script>
</body>

</html>