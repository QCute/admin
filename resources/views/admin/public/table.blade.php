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
            min-height: calc(100vh - 80px);
            display: flex; 
            flex-direction: column;
            /* justify-content: center; */
            /* align-items: center; */
        }

        .pear-container .layui-card-body {
            height: 100%;
            display: flex;
            flex-direction: column;
            /* justify-content: center; */
            /* align-items: center; */
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

        /* form */
        .pear-container .layui-row-form {
            width: 100%;
            padding: 0px 0px;
        }

        .layui-inline {
            position: relative;
            display: inline-block;
            vertical-align: top;
        }

        /* chart */
        .pear-container .layui-row-chart {
            width: 100%;
            flex-grow: 1;
            padding: 8px 0px;
        }

        .pear-container .layui-row-chart .chart {
            width: 100%;
            height: 100%;
        }

        /* table */
        .pear-container .layui-row-table {
            width: 100%;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .pear-container .layui-row-table .table, 
        .pear-container .layui-row-table .layui-table-view {
            width: 100%;
        }

        .pear-container .layui-row-table .table-page {
            align-self: flex-end;
        }

        .pear-container .layui-row-table .table-page a,
        .pear-container .layui-row-table .table-page span,
        .pear-container .layui-row-table .table-page em {
            user-select: none;
        }

        .layui-table-click, .layui-table-hover, .layui-table[lay-even] tbody tr:nth-child(even) {
            background-color: unset;
        }

        .layui-table-click, .layui-table-hover, .layui-table[lay-even] tbody tr:nth-child(even) td:not([rowspan]) {
            background-color: #f8f8f8;
        }
    </style>
</head>

<body>
    <div class="pear-container">
        <div class="layui-card">
            <div class="layui-card-body">

                @if (!empty($form))
                <form id="table-search" class="layui-row layui-row-form layui-form flex-{{ $form->align }} {{ $form->hide ? 'layui-hide' : '' }}">

                    @foreach ($form->field as $item)
                    {!! $item->render() !!}
                    @endforeach

                    {{ csrf_field() }}

                    <div class="layui-form-item layui-{{ $form->inline ? 'inline' : 'block' }}">
                        <div class="{{ $form->inline ? '' : 'layui-input-block' }}">
                            <button type="reset" class="layui-btn layui-btn-primary">{{ trans('admin.form.button.reset') }}</button>
                            <button class="layui-btn" lay-submit lay-filter="table-search-submit">{{ trans('admin.form.button.submit') }}</button>
                        </div>
                    </div>

                </form>
                @endif

                @if (!empty($chart))
                <div class="layui-row layui-row-chart">
                    <div id="chart" class="chart"></div>
                </div>
                @endif

                <div class="layui-row layui-row-table">

                    @if (!collect($left)->isEmpty())
                    <script type="text/html" id="table-tool">
                        <div class="layui-btn-container">
                            @foreach ($left as $item)
                            {!! $item->render() !!}
                            @endforeach
                        </div>
                    </script>
                    @endif

                    <table id="table" class="table layui-hide" lay-filter="table"></table>

                    @foreach ($template as $item)
                    {!! $item->render() !!}
                    @endforeach

                    <script type="text/html" id="tag">
                        @{{#  if(typeof d[d.LAY_COL.field] === 'object') { }}
                            <span class="layui-btn layui-btn-xs layui-bg-blue" style="margin-right: 10px;">
                                @{{= d.LAY_COL.templetSchema.split('.').reduce((a, i) => a[i], d[d.LAY_COL.field]) }}
                            </span>
                        @{{#  } else { }}
                            <span class="layui-btn layui-btn-xs layui-bg-blue" style="margin-right: 10px;">
                                @{{= d[d.LAY_COL.field] }}
                            </span>
                        @{{#  } }}
                    </script>

                    <script type="text/html" id="tags">
                        @{{#  if(typeof d[d.LAY_COL.field] === 'object') { }}
                            @{{#  layui.each(d[d.LAY_COL.field], function(index, item){ }}
                                <span class="layui-btn layui-btn-xs layui-bg-blue" style="margin-right: 10px;">
                                    @{{= d.LAY_COL.templetSchema.split('.').reduce((a, i) => a[i], item) }}
                                </span>
                            @{{#  }); }}
                        @{{#  } else { }}
                            @{{#  layui.each(d[d.LAY_COL.field].split(d.LAY_COL.templetSchema).filter(token => token), function(index, item) { }}
                                <span class="layui-btn layui-btn-xs layui-bg-blue" style="margin-right: 10px;">
                                    @{{= item }}
                                </span>
                            @{{#  }); }}
                        @{{#  } }}
                    </script>

                    <script type="text/html" id="image">
                        @{{#  if(typeof d[d.LAY_COL.field] === 'object') { }}
                            <img src="@{{= d.LAY_COL.templetSchema.split('.').reduce((a, i) => a[i], d[d.LAY_COL.field]) }}" style="width: 24px; height: 24px" lay-on="photos">
                        @{{#  } else { }}
                            <img src="@{{= d[d.LAY_COL.field] }}" style="width: 24px; height: 24px" lay-on="photos">
                        @{{#  } }}
                    </script>

                    <script type="text/html" id="images">
                        @{{#  if(typeof d[d.LAY_COL.field] === 'object') { }}
                            @{{#  layui.each(d[d.LAY_COL.field], function(index, item){ }}
                                <img src="@{{= d.LAY_COL.templetSchema.split('.').reduce((a, i) => a[i], item) }}" style="width: 24px; height: 24px" lay-on="photos">
                            @{{#  }); }}
                        @{{#  } else { }}
                            @{{#  layui.each(d[d.LAY_COL.field].split(d.LAY_COL.templetSchema).filter(token => token), function(index, item) { }}
                                <img src="@{{= item }}" style="width: 24px; height: 24px" lay-on="photos">
                            @{{#  }); }}
                        @{{#  } }}
                    </script>

                    <script type="text/html" id="icon">
                        @{{#  if(typeof d[d.LAY_COL.field] === 'object') { }}
                            <i class="@{{= d.LAY_COL.templetSchema.split('.').reduce((a, i) => a[i], d[d.LAY_COL.field]) }}"></i>
                        @{{#  } else { }}
                            <i class="@{{= d[d.LAY_COL.field] }}"></i>
                        @{{#  } }}
                    </script>

                    <script type="text/html" id="icons">
                        @{{#  if(typeof d[d.LAY_COL.field] === 'object') { }}
                            @{{#  layui.each(d[d.LAY_COL.field], function(index, item){ }}
                                <i class="@{{= d.LAY_COL.templetSchema.split('.').reduce((a, i) => a[i], item) }}"></i>
                            @{{#  }); }}
                        @{{#  } else { }}
                            @{{#  layui.each(d[d.LAY_COL.field].split(d.LAY_COL.templetSchema).filter(token => token), function(index, item) { }}
                                <i class="@{{= item }}"></i>
                            @{{#  }); }}
                        @{{#  } }}
                    </script>

                    @if (!collect($operation)->isEmpty())
                    <script type="text/html" id="table-operation">
                        <div class="layui-clear-space">
                            @foreach ($operation as $item)
                            @if ($item == 'show')
                            <a class="layui-btn layui-btn-xs layui-primary" lay-event="LAY_TABLE_SHOW">
                                <i class="layui-icon layui-icon-list"></i>{{ trans('admin.table.show') }}
                            </a>
                            @elseif ($item == 'edit')
                            <a class="layui-btn layui-btn-xs layui-bg-blue" lay-event="LAY_TABLE_EDIT">
                                <i class="layui-icon layui-icon-edit"></i>{{ trans('admin.table.edit') }}
                            </a>
                            @elseif ($item == 'delete')
                            <a class="layui-btn layui-btn-xs layui-bg-red" lay-event="LAY_TABLE_DELETE">
                                <i class="layui-icon layui-icon-delete"></i>{{ trans('admin.table.delete') }}
                            </a>
                            @else
                            <a class="layui-btn layui-btn-xs layui-bg-{{ $item->color }} lay-btn-event" lay-event="LAY_TABLE_{{ $item->event }}" lay-event-value="{{ $item->value }}">
                                <i class="layui-icon layui-icon-{{ $item->icon }}"></i>{{ $item->title }}
                            </a>
                            @endif
                            @endforeach
                        </div>
                    </script>
                    @endif

                    @if (!empty($paginate))
                    <div id="table-page" class="table-page"></div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <script>
        layui.use(['admin', 'popup', ], function () {
            const admin = layui.admin;
            const popup = layui.popup;
            const table = layui.table;

            @foreach ($form->field ?? [] as $item)
            {!! $item->run() !!}
            @endforeach

            // form
            @if (!empty($form))
            layui.form.on('submit(table-search-submit)', function (data) {
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

            @if (!empty($chart))
            const chartElement = document.querySelector('#chart');
            const charts = echarts.init(chartElement, 'walden');
            charts.setOption(@json($chart));
            // resize
            (new ResizeObserver(() => charts.resize())).observe(chartElement);
            @endif

            @foreach ($script as $item)
            {!! $item->render() !!}
            @endforeach

            // table
            table.render({
                id: 'table',
                elem: '#table',
                even: true,

                @if (!collect($left)->isEmpty())
                toolbar: '#table-tool',
                @elseif (!collect($right)->isEmpty())
                toolbar: true,
                @endif

                @if (!collect($right)->isEmpty())
                defaultToolbar: [
                    @foreach ($right as $item)
                    @if ($item == 'filter')
                    'filter', 
                    @elseif ($item == 'create')
                    {
                        title: '{{ trans('admin.table.create') }}',
                        layEvent: 'LAY_TABLE_CREATE',
                        icon: 'layui-icon-add-circle-fine'
                    },
                    @elseif ($item == 'download')
                    {
                        title: '{{ trans('admin.table.download') }}',
                        layEvent: 'LAYTABLE_EXPORT',
                        icon: 'layui-icon-download-circle'
                    },
                    @elseif ($item == 'export')
                    {
                        title: '{{ trans('admin.table.export') }}',
                        layEvent: 'LAY_TABLE_EXPORT',
                        icon: 'layui-icon-export'
                    },
                    @elseif ($item == 'import')
                    {
                        title: '{{ trans('admin.table.import') }}',
                        layEvent: 'LAY_TABLE_IMPORT',
                        icon: 'layui-icon-upload-drag'
                    },
                    @elseif ($item == 'search')
                    {
                        title: '{{ trans('admin.table.search') }}',
                        layEvent: 'LAY_TABLE_SEARCH',
                        icon: 'layui-icon-search'
                    },
                    @endif
                    @endforeach
                ],
                @endif

                cols: [@json($header)],
                data: @json($data),
                done: function (result, current, count, origin) {

                    const fields = @json($group ?? []);
                    if(fields.length == 0) return;

                    function deepMerge(target, source) {
                        for (const key in source) {
                            if (source[key] instanceof Object && target[key]) {
                                deepMerge(target[key], source[key]);
                            } else {
                                target[key] = source[key];
                            }
                        }
                        return target;
                    }

                    function reduce(header, subset, columns, depth, offset, rules) {
                        const next = columns[depth];
                        if(!next) return rules;

                        const name = header.find(item => item.field == next).field;
                        const columnIndex = header.findIndex(item => item.field == next);
                 
                        let span = 1;
                        let previous = null;
                        for(let rowIndex = 0; rowIndex < subset.length; rowIndex++) {
                            const item = subset[rowIndex];

                            if(previous === item[name]) {
                                span++;
                                continue;
                            }

                            if(previous === null) {
                                previous = item[name];
                                continue;
                            }

                            if(span == 1) {
                                previous = item[name];
                                continue;
                            }

                            const slice = subset.slice(rowIndex - span, rowIndex);
                            const part = Object.fromEntries( slice.map((_, index) => { 
                                const rulesRowIndex = offset + rowIndex - span + index;
                                const attribute = index == 0 ? { span } : { display: 'none' };
                                return [rulesRowIndex, Object.fromEntries([[columnIndex, attribute]])]; 
                            }));

                            rules = reduce(header, slice, columns, depth + 1, offset + rowIndex - span, deepMerge(rules, part));

                            previous = item[name];
                            span = 1;
                        }

                        if(span == 1) {
                            return rules;
                        }

                        const slice = subset.slice(subset.length - span, subset.length);
                        const part = Object.fromEntries(slice.map((_, index) => {
                            const rulesRowIndex = offset + subset.length - span + index;
                            const attribute = index == 0 ? { span } : { display: 'none' };
                            return [rulesRowIndex, Object.fromEntries([[columnIndex, attribute]])];
                        }));

                        return reduce(header, slice, columns, depth + 1, offset + subset.length - span, deepMerge(rules, part));
                    }

                    const rules = reduce(this.cols[0], result.data, fields, 0, 0, {});

                    const container = document.querySelector('#table').parentElement;
                    const tableRowElementList = container.querySelectorAll('.layui-table tbody tr');
                    for (let rowIndex = 0; rowIndex < tableRowElementList.length; rowIndex++) {
                        const rowData = rules[rowIndex];
                        if(!rowData) continue;

                        const tableRowElement = tableRowElementList[rowIndex];
                        tableRowElement.classList.add(`layui-table-row-${rowIndex}`);
                        const tableDataElementList = tableRowElement.querySelectorAll('td');
                        for (let dataIndex = 0; dataIndex < tableDataElementList.length; dataIndex++) {
                            const tableDataElement = tableDataElementList[dataIndex];

                            // reset attributes
                            const attribute = rowData[dataIndex];

                            if(!attribute) continue;

                            if(attribute.span) {
                                tableDataElement.setAttribute('rowspan', attribute.span);
                                tableDataElement.classList.add(`layui-table-row-${rowIndex}`);
                                for(let i = 1; i < attribute.span; i++) {
                                    tableDataElement.classList.add(`layui-table-row-${rowIndex + i}`);
                                }
                            }

                            if(attribute.display) {
                                tableDataElement.style.display = attribute.display;
                            }
                        }
                    }

                    // the row span hover color
                    container.querySelectorAll('td').forEach(td => {
                        td.addEventListener('mouseover', function () {
                            for(const cls of this.rowSpan == 1 ? this.parentElement.classList : this.classList) {
                                if(!cls.startsWith('layui-table-row')) continue;
                                container.querySelectorAll(`.${cls}`).forEach(td => {
                                    td.classList.add('layui-table-hover');
                                });
                            }
                        });
                        td.addEventListener('mouseout', function () {
                            for(const cls of this.rowSpan == 1 ? this.parentElement.classList : this.classList) {
                                if(!cls.startsWith('layui-table-row')) continue;
                                container.querySelectorAll(`.${cls}`).forEach(td => {
                                    td.classList.remove('layui-table-hover');
                                });
                            }
                        });
                    });
                }
            });

            // tool bar
            @if (!collect($left)->isEmpty() || !collect($right)->isEmpty())
            table.on('toolbar(table)', function (value) {
                switch (value.event) {
                    case 'LAY_TABLE_CREATE': {
                        admin.changePage({ url: `${location.pathname}/create`, type: 1 });
                    }; break;
                    case 'LAY_TABLE_EXPORT': {
                        location.href = `${location.pathname}/export?${location.search}`;
                    }; break;
                    case 'LAY_TABLE_IMPORT': {
                        location.href = `${location.pathname}/import?${location.search}`;
                    }; break;
                    case 'LAY_TABLE_SEARCH': {
                        const search = document.querySelector('#table-search');
                        search.classList.toggle('layui-hide');
                    }; break;
                };
            });
            @endif

            // cell too bar
            @if (!collect($operation)->isEmpty())
            table.on('tool(table)', function (value) {
                switch (value.event) {
                    case 'LAY_TABLE_TO': {
                        const element = value.tr[0].lastChild.querySelector('.lay-btn-event');
                        const url = element.getAttribute('lay-event-value');
                        location.href = `${url}/${value.data.id}`;
                    };break;
                    case 'LAY_TABLE_CHANGE': {
                        const element = value.tr[0].lastChild.querySelector('.lay-btn-event');
                        const url = element.getAttribute('lay-event-value');
                        admin.changePage({ url: `${url}/${value.data.id}`, type: 1 });
                    };break;
                    case 'LAY_TABLE_SHOW': {
                        admin.changePage({ url: `${location.pathname}/${value.data.id}`, type: 1 });
                    }; break;
                    case 'LAY_TABLE_EDIT': {
                        admin.changePage({ url: `${location.pathname}/${value.data.id}/edit`, type: 1 });
                    }; break;
                    case 'LAY_TABLE_DELETE': {
                        layer.confirm('{{ trans('admin.table.delete') }}: ' + value.data.id, async function (index) {

                            // /path/to/name/create => /path/to/name
                            const response = await fetch(`${location.pathname}/${value.data.id}`, {
                                method: 'DELETE',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                },
                                body: JSON.stringify({ _token: '{{ csrf_token() }}' })
                            });

                            if (!response.ok) {
                                // popup.failure(`{{ trans('admin.status.failure') }}: ${response.status}: ${response.statusText}`);
                                const login = (new DOMParser()).parseFromString(state.responseText, 'text/html').head.querySelector('meta[name="login"]');
                                if(login) {
                                    location.href = "{{ admin_path(config('admin.auth.redirect_to')) }}";
                                    return false;
                                }
                                // error reload
                                document.write(state.responseText);
                                return false;
                            }

                            const { code, msg } = await response.json();

                            if (code || msg) {
                                popup.failure(`{{ trans('admin.status.failure') }}: ${msg}`);
                                return false;
                            }

                            popup.success('{{ trans('admin.status.success') }}', function() {
                                // remove row dom
                                value.del();

                                // remove view
                                layer.close(index);
                            });

                            return false;
                        });
                    }; break;
                }
            });
            @endif

            layui.util.on('lay-on', {
                photos: function(element) {
                    layer.photos({
                        photos: {
                            start: 0,
                            data: [{
                                alt: '',
                                pid: 0,
                                src: element[0].src,
                            }],
                        }
                    });
                }
            });

            @if (!empty($paginate))
            layui.laypage.render({
                elem: 'table-page',
                count: {{ $paginate->total }},
                curr: {{ $paginate->current }},
                limit: {{ $paginate->limit }},
                limits: @json($paginate->limits),
                layout: @json($paginate->layout),
                jump: function (value, first) {
                    // skip first
                    if (first) return;

                    const params = new URL(location.href).searchParams;

                    // rebuild params
                    params.set('page', value.curr);
                    params.set('perPage', value.limit);
                    const search = Array.from(params).map(([k, v]) => `${k}=${v}`).join('&');

                    // reload view
                    admin.changePage({ url: `${location.pathname}?${search}`, type: 1 });
                    return false;
                }
            });
            @endif
        });
    </script>
</body>

</html>