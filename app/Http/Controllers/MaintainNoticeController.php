<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class MaintainNoticeController extends Controller
{
    public function get(): JsonResponse
    {
        $platform = request()->input("platform", "");
        $data = DB::select("SELECT `title`, `content`, `start_time`, `end_time` FROM `maintain_notice` WHERE `platform` = :platform ", ["platform" => $platform]);
        return response()->json($data);
    }
}
