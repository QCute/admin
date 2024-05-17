<?php

namespace App\Api\Services;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;

class TikTokService
{
    /**
     * The config
     *
     * @var array $config
     */
    private $config = [];

    /**
     * The request url
     *
     * @var array $url
     */
    private static $url = 'https://minigame.zijieapi.com/mgplatform/api/apps/';

    /**
     * The access token
     *
     * @var array $accessToken
     */
    private $accessToken = [];

    public function __construct(array $config) 
    {
        $this->config = $config;
    }

    /**
     * The request
     *
     * @return array
     */
    public function request(string $method = 'POST', string $path = '', array $headers = [], array $data = [], int $timeout = 5): array
    {
        $result = [];

        if($method == 'GET') {
            $query = http_build_query($data);
            $result = Http::withHeaders($headers)->timeout($timeout)->get(Self::$url . $path, $query)->json();
        }
        
        if($method == 'POST') {
            $result = Http::withHeaders($headers)->timeout($timeout)->post(Self::$url . $path, $data)->json();
        }

        $code = $result['error'] ?? $result['err_no'] ?? 0;
        if($code > 0) {
            $err = $result['error'] ?? $result['err_no'] ?? 0;
            $msg = $result['message'] ?? $result['err_tips'] ?? '';
            throw new \Exception($msg, $err);
        }

        return $result['data'] ?? $result;
    }
    
    public function getAccessToken(): array
    {
        $name = config('api.database.connection');
        $connection = DB::connection($name);

        if(empty($this->accessToken)) {
            if(!Schema::connection($name)::hasTable('table_name')) {
                Schema::create(config('web.database.log_table'), function (Blueprint $table) {
                    $table->integer('id')->unsigned()->comment('')->autoIncrement();
                    $table->string('app_id')->unsigned()->default(0)->comment('');
                    $table->string('app_secret')->default('')->comment('');
                    $table->string(column: 'platform')->default('')->comment('');
                    $table->string('access_token')->default('')->comment('');
                    $table->integer('time')->default('')->comment('');
                    $table->integer('expire_in')->default('')->comment('');
                    $table->index('created_time');
                    $table->index('updated_time');
                    $table->index('deleted_time');
                });
            }

            // get token
            $this->accessToken = $connection
                ->table('access_token')
                ->where('app_id', $this->config['appId'])
                ->first() ?? [];
        }

        $time = $this->accessToken['time'] ?? 0;
        $expiresIn = $this->accessToken['expires_in'] ?? 0;
        if($time + $expiresIn < time()) {
            return $this->accessToken;
        }

        $data = [
            'appid' => $this->config['appId'],
            'secret' => $this->config['appSecret'],
            'grant_type' => 'client_credential',
        ];
        $result = $this->request('POST', 'v2/token', [], $data);
        $result['time'] = time() - 60;

        // save token
        $connection
            ->table('access_token')
            ->updateOrInsert(
                [
                    'app_id' => $this->config['appId']
                ],
                [
                    'app_id' => $this->config['appId'],
                    'app_secret' => $this->config['appSecret'],
                    'platform' => 'TikTok',
                    'access_token' => $result['access_token'],
                    'time' => $result['time'],
                    'expire_in' => $result['expire_in'],
                ],
            );

        return $result;
    }

    public function login(string $code): array
    {
        $data = [
            'appid' => $this->config['appId'],
            'secret' => $this->config['appSecret'],
            'code' => $code,
        ];
        return $this->request('POST', 'jscode2session', [], $data);
    }
}
