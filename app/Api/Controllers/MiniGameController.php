<?php

namespace App\Api\Controllers;

use App\Api\Services\TikTokService;
use App\Api\Services\WeChatService;
use App\Api\Services\GameTokService;
use Illuminate\Http\Request;

class MiniGameController extends Controller
{
    public function weChatLogin(Request $request)
    {
        $appId = $request->input('app_id');

        if(empty($appId)) {
            return ['code' => 1, 'msg' => 'app_id is required'];
        }

        $config = [
            'appId' => env('WECHAT_APP_ID' . '_' . $appId),
            'appSecret' => env('WECHAT_APP_SECRET' . '_' . $appId),
        ];

        if(empty($config['appId']) || empty($config['appSecret'])) {
            return ['code' => 1, 'msg' => 'app_id is invalid'];
        }

        $code = $request->input('code');

        if(empty($code)) {
            return ['code' => 1, 'msg' => 'code is required'];
        }

        $result = new WeChatService($config)->login($code);

        $key = $request->input('key');
        if(empty($key) || empty(env('WECHAT_KEY')) || $key !== env('WECHAT_KEY')) {
            unset($result['session_key']);
        }

        return $result;
    }

    public function tikTokLogin(Request $request)
    {
        $appId = $request->input('app_id');

        if(empty($appId)) {
            return ['code' => 1, 'msg' => 'app_id is required'];
        }

        $config = [
            'appId' => env('TIKTOK_APP_ID' . '_' . $appId),
            'appSecret' => env('TIKTOK_APP_SECRET' . '_' . $appId),
        ];

        if(empty($config['appId']) || empty($config['appSecret'])) {
            return ['code' => 1, 'msg' => 'app_id is invalid'];
        }

        $code = $request->input('code');

        if(empty($code)) {
            return ['code' => 1, 'msg' => 'code is required'];
        }

        $result = new TikTokService($config)->login($code);

        $key = $request->input('key');
        if(empty($key) || empty(env('TIKTOK_KEY')) || $key !== env('TIKTOK_KEY')) {
            unset($result['session_key']);
        }

        return $result;
    }

    public function gameTokLogin(Request $request) 
    {
        $appId = $request->input('app_id', '');
        $uid = $request->input('uid', '');
        $accessToken = $request->input('access_token', '');

        if(empty($appId)) {
            return ['code' => 1, 'msg' => 'app_id is required'];
        }

        if(empty($uid)) {
            return ['code' => 1, 'msg' => 'uid is required'];
        }

        if(empty($accessToken)) {
            return ['code' => 1, 'msg' => 'access_token is required'];
        }

        return new GameTokService(
            env('GAMETOK_APP_ID' . '_' . $appId),     // params.gameId 
            env('GAMETOK_APP_KEY' . '_' . $appId),   // Configuration -> appKey
            $uid,                                            // params.uid
            $accessToken,                            // params.token
        )->getProfile();
    }

    public function gameTokProductList(Request $request) 
    {
        $appId = $request->input('app_id', '');

        if(empty($appId)) {
            return ['code' => 1, 'msg' => 'app_id is required'];
        }

        return new GameTokService(
            env('GAMETOK_APP_ID' . '_' . $appId),     // params.gameId 
            env('GAMETOK_APP_KEY' . '_' . $appId),   // Configuration -> appKey
        )->productList();
    }

    public function gameTokPurchase(Request $request) 
    {
        $appId = $request->input('app_id', '');
        $uid = $request->input('uid', '');
        $accessToken = $request->input('access_token', '');
        $sessionId = $request->input('session_id', '');
        $productId = $request->input('product_id', '');

        if(empty($appId)) {
            return ['code' => 1, 'msg' => 'app_id is required'];
        }

        if(empty($uid)) {
            return ['code' => 1, 'msg' => 'uid is required'];
        }

        if(empty($accessToken)) {
            return ['code' => 1, 'msg' => 'access_token is required'];
        }

        if(empty($sessionId)) {
            return ['code' => 1, 'msg' => 'session_id is required'];
        }

        if(empty($productId)) {
            return ['code' => 1, 'msg' => 'product_id is required'];
        }

        return new GameTokService(
            env('GAMETOK_APP_ID' . '_' . $appId),     // params.gameId 
            env('GAMETOK_APP_KEY' . '_' . $appId),   // Configuration -> appKey
            $uid,                                            // params.uid
            $accessToken,                            // params.token
            $sessionId,                                // params.sessionId
            $productId                                 // Product Identification
        )->purchase();
    }
}
