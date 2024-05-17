<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title></title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <style>
        .pear-container > .layui-card > .layui-card-body {
            min-height: calc(100vh - 100px);
            display: flex;
            flex-direction: column;
            /* justify-content: center; */
            /* align-items: center; */
        }

        .pear-container .layui-row-form {
            width: 100%;
            padding: 0px 0px;
            flex-grow: 1;
        }

        .layui-form-label {
            width: 120px !important;
        }

        .layui-input-block {
            margin-left: 150px !important;
        }
    </style>
</head>

<body>
    <div class="pear-container">
        <div class="layui-card">
            <div class="layui-card-header">{{ $name }}</div>
            <div class="layui-card-body">

                @if($success = session()->get('success'))
                <blockquote class="layui-elem-quote" style="position: relative; border-left: 5px solid #16b777;">
                    <div style="position: absolute; width: 24px; height: 24px; display: flex; justify-content: center; align-items: center; top: 8px; right: 8px; cursor: pointer; font-size: 18px;" onclick="this.parentElement.style.display = 'none'">×</div>
                    <h3><i class="icon fa fa-check-circle" style="color: #16b777; margin-right: 6px"></i>{{ \Illuminate\Support\Arr::get($success->get('title'), 0) }}</h3>
                    <p>{!!  \Illuminate\Support\Arr::get($success->get('message'), 0) !!}</p>
                </blockquote>
                @endif

                @if($info = session()->get('info'))
                <blockquote class="layui-elem-quote" style="position: relative; border-left: 5px solid #31bdec;">
                <div style="position: absolute; width: 24px; height: 24px; display: flex; justify-content: center; align-items: center; top: 8px; right: 8px; cursor: pointer; font-size: 18px;" onclick="this.parentElement.style.display = 'none'">×</div>
                    <h3><i class="icon fa fa-info-circle" style="color: #31bdec; margin-right: 6px"></i>{{ \Illuminate\Support\Arr::get($success->get('title'), 0) }}</h3>
                    <p>{!!  \Illuminate\Support\Arr::get($success->get('message'), 0) !!}</p>
                </blockquote>
                @endif

                @if($warning = session()->get('warning'))
                <blockquote class="layui-elem-quote" style="position: relative; border-left: 5px solid #ffb800;">
                <div style="position: absolute; width: 24px; height: 24px; display: flex; justify-content: center; align-items: center; top: 8px; right: 8px; cursor: pointer; font-size: 18px;" onclick="this.parentElement.style.display = 'none'">×</div>
                    <h3><i class="icon fa fa-warning" style="color: #ffb800; margin-right: 6px"></i>{{ \Illuminate\Support\Arr::get($success->get('title'), 0) }}</h3>
                    <p>{!!  \Illuminate\Support\Arr::get($success->get('message'), 0) !!}</p>
                </blockquote>
                @endif

                @if($error = session()->get('error'))
                <blockquote class="layui-elem-quote" style="position: relative; border-left: 5px solid #ff5722;">
                <div style="position: absolute; width: 24px; height: 24px; display: flex; justify-content: center; align-items: center; top: 8px; right: 8px; cursor: pointer; font-size: 18px;" onclick="this.parentElement.style.display = 'none'">×</div>
                    <h3><i class="icon fa fa-ban" style="color: #ff5722; margin-right: 6px"></i>{{ \Illuminate\Support\Arr::get($success->get('title'), 0) }}</h3>
                    <p>{!!  \Illuminate\Support\Arr::get($success->get('message'), 0) !!}</p>
                </blockquote>
                @elseif ($errors = session()->get('errors'))
                    @if ($errors->hasBag('error'))
                    <blockquote class="layui-elem-quote" style="position: relative; border-left: 5px solid #ff5722;">
                    <div style="position: absolute; width: 24px; height: 24px; display: flex; justify-content: center; align-items: center; top: 8px; right: 8px; cursor: pointer; font-size: 18px;" onclick="this.parentElement.style.display = 'none'">×</div>
                        <h3><i class="icon fa fa-ban" style="color: #ff5722; margin-right: 6px"></i>{{ \Illuminate\Support\Arr::get($success->get('title'), 0) }}</h3>
                        @foreach($errors->getBag("error")->toArray() as $message)
                            <p>{!!  \Illuminate\Support\Arr::get($message, 0) !!}</p>
                        @endforeach
                    </blockquote>
                    @endif
                @endif

                <form id="form" class="layui-row layui-row-form layui-form" onsubmit="return false;">

                    @foreach ($field as $item)
                    {!! $item->render() !!}
                    @endforeach

                    {{ csrf_field() }}

                    <div class="layui-form-item {{ $disabled ? 'layui-hide' : '' }}" >
                        <div class="layui-input-block">
                            <button type="reset" lay-filter="form-reset" class="layui-btn layui-btn-primary">{{ trans('admin.form.button.reset') }}</button>
                            <button class="layui-btn" lay-submit lay-filter="form-submit">{{ trans('admin.form.button.submit') }}</button>
                        </div>
                    </div>

                </form>

            </div>
        </div>
    </div>
    <script>
        layui.use(['admin', 'popup', ], function () {
            const admin = layui.admin;
            const popup = layui.popup;

            @foreach ($field as $item)
            {!! $item->run() !!}
            @endforeach

            @foreach ($script as $item)
            {!! $item->render() !!}
            @endforeach

            layui.form.render();

            layui.form.on('submit(form-submit)', async function (data) {
                // /path/to/name/create => /path/to/name
                const url = location.pathname.split('/').slice(0, -1).join('/');
                // remove empty fields
                const group = @json($group);
                const field = Object
                    .entries(data.field)
                    .filter(([k, v]) => v.toString().length)
                    .filter(([k, v]) => !['layTransferLeftCheckAll', 'layTransferRightCheckAll', 'layTransferLeftCheck', 'layTransferRightCheck'].includes(k))
                    .reduce((acc, [k, v]) => {
                        const [_, name, index] = k.match(/(.*?)\[(\d+)\]/) || ['', k, undefined];
                        if(typeof index == 'undefined') {
                            acc[name] = v;
                        } else {
                            const [groupByName, ] = Object.entries(group).find(([group, names]) => names.find(i => i == name)) || [];
                            if(groupByName) {
                                const list = acc[groupByName] || [];
                                const element = list[parseInt(index)] || {};
                                element[name] = v;
                                list[parseInt(index)] = element;
                                acc[groupByName] = list;
                            } else {
                                const previous = acc[name] || [];
                                previous[parseInt(index)] = v;
                                acc[name] = previous;
                            }
                        }
                        return acc;
                    }, {});

                const response = await fetch(`${url}`, {
                    method: '{{ $method }}',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify(field),
                });

                if (!response.ok) {
                    // popup.failure(`{{ trans('admin.status.failure') }}: ${response.status}: ${response.statusText}`);
                    // need to redirect to login page if token expired
                    const login = (new DOMParser()).parseFromString(state.responseText, 'text/html').head.querySelector('meta[name="login"]');
                    if(login) {
                        location.href = "{{ admin_path(config('admin.auth.redirect_to')) }}";
                        return false;
                    }
                    // error reload
                    document.write(state.responseText);
                    return false;
                }

                const { code, msg } = result = await response.json();

                if (code || msg) {
                    popup.failure(`{{ trans('admin.status.result') }}: ${msg}`);
                    console.error(result);
                    return false;
                }

                popup.success('{{ trans('admin.status.success') }}', function() {
                    // get:    /path            => index
                    // get:    /path/create     => create
                    // post:   /path/{id}       => show
                    // get:    /path/{id}/edit  => edit
                    // patch:  /path/{id}       => update
                    // delete: /path/{id}       => destroy
                    const path = location.pathname.split('/');
                    const type = path.pop();
                    const offset = ({ 'create': path.length, 'edit': -1 })[type] || path.length;
                    const url = path.slice(0, offset).join('/');
                    admin.changePage({ url, type: 1 });
                });

                return false;
            });
        });
    </script>
</body>

</html>