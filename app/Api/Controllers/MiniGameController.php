<?php

namespace App\Api\Controllers;

use App\Api\Services\TikTokService;
use App\Api\Services\WeChatService;
use Illuminate\Http\Request;

class MiniGameController extends Controller
{
    public function weChatLogin(Request $request)
    {
        $config = [
            'appId' => env('WECHAT_APP_ID'),
            'appSecret' => env('WECHAT_APP_SECRET'),
        ];

        $code = $request->input('code');
        $result = new WeChatService($config)->login($code);

        $key = $request->input('key');
        if(empty($key) || empty(env('WECHAT_KEY')) || $key !== env('WECHAT_KEY')) {
            unset($result['session_key']);
        }

        return $result;
    }

    public function tikTokLogin(Request $request)
    {
        $config = [
            'appId' => env('TIKTOK_APP_ID'),
            'appSecret' => env('TIKTOK_APP_SECRET'),
        ];

        $code = $request->input('code');
        $result = new TikTokService($config)->login($code);

        $key = $request->input('key');
        if(empty($key) || empty(env('TIKTOK_KEY')) || $key !== env('TIKTOK_KEY')) {
            unset($result['session_key']);
        }

        return $result;
    }
}
