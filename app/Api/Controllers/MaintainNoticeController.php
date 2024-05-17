<?php

namespace App\Api\Controllers;

use App\Api\Models\MaintainNoticeModel;
use Illuminate\Http\Request;

class MaintainNoticeController extends Controller
{
    public function get(Request $request)
    {
        $channel = $request->input('channel', '');
        $data = MaintainNoticeModel::getNotices($channel);

        return $data;
    }
}
