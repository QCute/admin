<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class MaintainNoticeController extends Controller
{
    /**
     * @OA\Get(
     *     path = "/api/maintain-notice",
     *     summary = "维护公告",
     *     @OA\Parameter(
     *         description = "渠道",
     *         in = "query",
     *         name = "channel",
     *         required = true,
     *         @OA\Schema(type = "string"),
     *         @OA\Examples(example = "channel", value = "deal", summary = "渠道"),
     *     ),
     *     @OA\Response(
     *         response = 200,
     *         description = "OK",
     *         @OA\JsonContent(
     *             @OA\Examples(example = "result", value = {"title": "this is title", "content": "this is content", "start_time": 1649308407, "end_time": 1649394797}, summary = "维护公告"),
     *         )
     *     )
     * )
     */
    public function get(): JsonResponse
    {
        $channel = request()->input("channel", "");
        $data = DB::select("SELECT `title`, `content`, `start_time`, `end_time` FROM `maintain_notice` WHERE `channel` = :channel ", ["channel" => $channel]);
        return response()->json($data);
    }
}
