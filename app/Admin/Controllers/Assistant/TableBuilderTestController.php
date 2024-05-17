<?php

namespace App\Admin\Controllers\Assistant;

use App\Admin\Controllers\Controller;
use App\Admin\Builders\Table;
use App\Admin\Builders\Table\Header;

class TableBuilderTestController extends Controller
{
    public function index()
    {
        $header = collect([
            (new Header())->field('os')->title(trans('admin.statistic.os'))->align(),
            (new Header())->field('platform')->title(trans('admin.statistic.platform'))->align(),
            (new Header())->field('channel')->title(trans('admin.statistic.channel'))->align(),
            (new Header())->field('number')->title(trans('admin.statistic.number'))->align(),
        ]);

        $data = [
            ['os' => 'Android', 'platform' => 'WeAd', 'channel' => 'Tencent', 'number' => 100],
            ['os' => 'Android', 'platform' => 'WeAd', 'channel' => 'Tencent', 'number' => 200],
            ['os' => 'Android', 'platform' => 'WeAd', 'channel' => 'Tencent', 'number' => 300],

            ['os' => 'Android', 'platform' => 'WeAd', 'channel' => 'ByteDance', 'number' => 400],
            ['os' => 'Android', 'platform' => 'WeAd', 'channel' => 'ByteDance', 'number' => 500],
            ['os' => 'Android', 'platform' => 'WeAd', 'channel' => 'ByteDance', 'number' => 600],

            ['os' => 'Android', 'platform' => 'WeAd', 'channel' => 'Kwai', 'number' => 700],
            ['os' => 'Android', 'platform' => 'WeAd', 'channel' => 'Kwai', 'number' => 800],
            ['os' => 'Android', 'platform' => 'WeAd', 'channel' => 'Kwai', 'number' => 900],


            ['os' => 'Android', 'platform' => 'OceanEngine', 'channel' => 'Tencent', 'number' => 100],
            ['os' => 'Android', 'platform' => 'OceanEngine', 'channel' => 'Tencent', 'number' => 200],
            ['os' => 'Android', 'platform' => 'OceanEngine', 'channel' => 'Tencent', 'number' => 300],

            ['os' => 'Android', 'platform' => 'OceanEngine', 'channel' => 'ByteDance', 'number' => 400],
            ['os' => 'Android', 'platform' => 'OceanEngine', 'channel' => 'ByteDance', 'number' => 500],
            ['os' => 'Android', 'platform' => 'OceanEngine', 'channel' => 'ByteDance', 'number' => 600],

            ['os' => 'Android', 'platform' => 'OceanEngine', 'channel' => 'Kwai', 'number' => 700],
            ['os' => 'Android', 'platform' => 'OceanEngine', 'channel' => 'Kwai', 'number' => 800],
            ['os' => 'Android', 'platform' => 'OceanEngine', 'channel' => 'Kwai', 'number' => 900],


            ['os' => 'Android', 'platform' => 'Magnetic', 'channel' => 'Tencent', 'number' => 100],
            ['os' => 'Android', 'platform' => 'Magnetic', 'channel' => 'Tencent', 'number' => 200],
            ['os' => 'Android', 'platform' => 'Magnetic', 'channel' => 'Tencent', 'number' => 300],

            ['os' => 'Android', 'platform' => 'Magnetic', 'channel' => 'ByteDance', 'number' => 400],
            ['os' => 'Android', 'platform' => 'Magnetic', 'channel' => 'ByteDance', 'number' => 500],
            ['os' => 'Android', 'platform' => 'Magnetic', 'channel' => 'ByteDance', 'number' => 600],

            ['os' => 'Android', 'platform' => 'Magnetic', 'channel' => 'Kwai', 'number' => 700],
            ['os' => 'Android', 'platform' => 'Magnetic', 'channel' => 'Kwai', 'number' => 800],
            ['os' => 'Android', 'platform' => 'Magnetic', 'channel' => 'Kwai', 'number' => 900],




            ['os' => 'iOS', 'platform' => 'WeAd', 'channel' => 'Tencent', 'number' => 100],
            ['os' => 'iOS', 'platform' => 'WeAd', 'channel' => 'Tencent', 'number' => 200],
            ['os' => 'iOS', 'platform' => 'WeAd', 'channel' => 'Tencent', 'number' => 300],

            ['os' => 'iOS', 'platform' => 'WeAd', 'channel' => 'ByteDance', 'number' => 400],
            ['os' => 'iOS', 'platform' => 'WeAd', 'channel' => 'ByteDance', 'number' => 500],
            ['os' => 'iOS', 'platform' => 'WeAd', 'channel' => 'ByteDance', 'number' => 600],

            ['os' => 'iOS', 'platform' => 'WeAd', 'channel' => 'Kwai', 'number' => 700],
            ['os' => 'iOS', 'platform' => 'WeAd', 'channel' => 'Kwai', 'number' => 800],
            ['os' => 'iOS', 'platform' => 'WeAd', 'channel' => 'Kwai', 'number' => 900],


            ['os' => 'iOS', 'platform' => 'OceanEngine', 'channel' => 'Tencent', 'number' => 100],
            ['os' => 'iOS', 'platform' => 'OceanEngine', 'channel' => 'Tencent', 'number' => 200],
            ['os' => 'iOS', 'platform' => 'OceanEngine', 'channel' => 'Tencent', 'number' => 300],

            ['os' => 'iOS', 'platform' => 'OceanEngine', 'channel' => 'ByteDance', 'number' => 400],
            ['os' => 'iOS', 'platform' => 'OceanEngine', 'channel' => 'ByteDance', 'number' => 500],
            ['os' => 'iOS', 'platform' => 'OceanEngine', 'channel' => 'ByteDance', 'number' => 600],

            ['os' => 'iOS', 'platform' => 'OceanEngine', 'channel' => 'Kwai', 'number' => 700],
            ['os' => 'iOS', 'platform' => 'OceanEngine', 'channel' => 'Kwai', 'number' => 800],
            ['os' => 'iOS', 'platform' => 'OceanEngine', 'channel' => 'Kwai', 'number' => 900],


            ['os' => 'iOS', 'platform' => 'Magnetic', 'channel' => 'Tencent', 'number' => 100],
            ['os' => 'iOS', 'platform' => 'Magnetic', 'channel' => 'Tencent', 'number' => 200],
            ['os' => 'iOS', 'platform' => 'Magnetic', 'channel' => 'Tencent', 'number' => 300],

            ['os' => 'iOS', 'platform' => 'Magnetic', 'channel' => 'ByteDance', 'number' => 400],
            ['os' => 'iOS', 'platform' => 'Magnetic', 'channel' => 'ByteDance', 'number' => 500],
            ['os' => 'iOS', 'platform' => 'Magnetic', 'channel' => 'ByteDance', 'number' => 600],

            ['os' => 'iOS', 'platform' => 'Magnetic', 'channel' => 'Kwai', 'number' => 700],
            ['os' => 'iOS', 'platform' => 'Magnetic', 'channel' => 'Kwai', 'number' => 800],
            ['os' => 'iOS', 'platform' => 'Magnetic', 'channel' => 'Kwai', 'number' => 900],
        ];

        $group = [
            'os',
            'platform',
            'channel',
        ];

        return (new Table())
            ->header($header)
            ->data($data)
            ->group($group)
            ->build();
    }
}
