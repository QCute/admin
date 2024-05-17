<?php

namespace App\Api\Services;

use Illuminate\Support\Facades\Http;

class GameTokService
{
    public $config = [];
    public $host = 'https://game-gateway.lobah.net';
    public $method = 'POST';
    public $zone = 'SA';

    public function __construct(string $appId, string $appKey, string $uid = '', string $accessToken = '', string $sessionId = '', string $productId = '') 
    {
        $this->config = [
            'appId' => $appId,
            'appKey' => $appKey,
            'uid' => $uid,
            'accessToken' => $accessToken,
            'sessionId' => $sessionId,
            'productId' => $productId
        ];
    }

    public function test(): array {
        $path = '/1.0/open-gateway/game/test';
        $postBody = '';
        return $this->httpPost($path, $postBody);
    }

    public function getProfile(): array {
        $path = '/1.0/open-gateway/game/get-profile';
        
        $postBody = [
            'app_id' => $this->config['appId'],
            'token' => $this->config['accessToken'],
            'uid' => $this->config['uid']
        ];

        return $this->httpPost($path, $postBody);
    }

    public function productList(): array {
        $path = '/1.0/open-gateway/game/product-list';
        
        $postBody = [
            'app_id' => $this->config['appId'],
        ];

        return $this->httpPost($path, $postBody);
    }

    public function purchase(): array {
        $referenceId = Self::generateReferenceID();
        $path = '/1.0/open-gateway/game/purchase';
        
        $postBody = [
            'app_id' => $this->config['appId'],
            'product_id' => $this->config['productId'],
            'reference_id' => $referenceId,
            'session_id' => $this->config['sessionId'],
            'uid' => $this->config['uid']
        ];

        return $this->httpPost($path, $postBody);
    }

    public function httpPost($requestPath, $postBody): array {
        // 构建请求参数
        $reqParams = [
            'access_token' => $this->config['accessToken'],
            'app_id' => $this->config['appId'],
            'nonce' => Self::generateNonce(),
            'ts' => (string) time(),
            'uid' => $this->config['uid'],
            'zone' => $this->zone
        ];

        // 排序参数
        ksort($reqParams);
        $reqParam = http_build_query($reqParams, '', '&');

        // 构建签名字符串
        $encodeStr = $this->method . $requestPath . $reqParam . json_encode($postBody);
        $urlEncodedData = urlencode($encodeStr);
        $digest = hash_hmac('md5', $urlEncodedData, $this->config['appKey']);

        $finalURL = $this->host . $requestPath . '?' . $reqParam . '&sig=' . $digest;

        return Http::withHeader('User-Agent', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)')
            ->withHeader('Content-Type', 'application/json; utf-8')
            ->withHeader('Accept', 'application/json')
            ->timeout(30)
            ->withoutVerifying()
            ->post($finalURL, $postBody)
            ->json();
    }

    public static function generateNonce(): string {
        $chars = '0123456789abcdefghijklmnopqrstuvwxyz';
        $len = strlen($chars);
        $result = '';
        for ($i = 0; $i < 16; $i++) {
            $result .= $chars[rand(0, $len - 1)];
        }
        return $result;
    }

    public static function generateReferenceID(): string {
        $chars = '0123456789abcdefghijklmnopqrstuvwxyz';
        $len = strlen($chars);
        $result = '';
        for ($i = 0; $i < 64; $i++) {
            $result .= $chars[rand(0, $len - 1)];
        }
        return $result;
    }
}
