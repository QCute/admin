<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title></title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <style>
        .pear-container .layui-card {
            height: calc(100vh - 80px);
            display: flex; 
            flex-direction: column;
            /* justify-content: center; */
            /* align-items: center; */
        }

        .pear-container .layui-card-body {
            height: 100%;
            display: flex; 
            flex-direction: column;
        }

        .flex-left {
            text-align: left;
        }

        .flex-center {
            text-align: center;
        }

        .flex-right {
            text-align: right;
        }

        .pear-container .layui-row-form {
            width: 100%;
            padding: 0px 0px;
        }

        .layui-inline {
            position: relative;
            display: inline-block;
            vertical-align: top;
        }

        .pear-container .layui-row-chart {
            width: 100%;
            padding: 0px 0px;
            flex-grow: 1;
        }

        .pear-container .layui-row-chart .chart {
            width: 100%;
            height: 100%;
        }
    </style>
</head>

<body>
    <div class="pear-container">
        <div class="layui-card">
            <div class="layui-card-body">

                @if (!empty($form))
                <form id="search" class="layui-row layui-row-form layui-form flex-{{ $form->align }} {{ $form->hide ? 'layui-hide' : '' }}">

                    @foreach ($form->field as $item)
                    {!! $item->render() !!}
                    @endforeach

                    {{ csrf_field() }}

                    <div class="layui-form-item layui-{{ $form->inline ? 'inline' : 'block' }} {{ $form->disabled ? 'layui-hide' : '' }}">
                        <div class="{{ $form->inline ? '' : 'layui-input-block' }}">
                            <button type="reset" class="layui-btn layui-btn-primary">{{ trans('admin.form.button.reset') }}</button>
                            <button class="layui-btn" lay-submit lay-filter="search-submit">{{ trans('admin.form.button.submit') }}</button>
                        </div>
                    </div>

                </form>
                @endif

                <div class="layui-row layui-row-chart">
                    <div id="chart" class="chart"></div>
                </div>

            </div>
        </div>
    </div>
    <script>
        layui.use(['admin', 'echarts', ], function () {
            const admin = layui.admin;

            @foreach ($form->field ?? [] as $item)
            {!! $item->run() !!}
            @endforeach

            // form
            @if (!empty($form))
            layui.form.on('submit(search-submit)', function (data) {
                // remove empty fields
                const query = Object
                    .entries(data.field)
                    .filter(([k, v]) => v)
                    .map(([k, v]) => `${k}=${v}`)
                    .join('&');

                admin.changePage({ url: `${location.pathname}?${query}`, type: 1 });

                return false;
            });
            @endif

            const chartElement = document.querySelector('#chart');
            const charts = echarts.init(chartElement, 'walden');
            charts.setOption(@json($chart));
            // resize
            (new ResizeObserver(() => charts.resize())).observe(chartElement);
        });
    </script>
</body>

</html>