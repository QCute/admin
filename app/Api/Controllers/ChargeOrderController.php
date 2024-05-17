<?php

namespace App\Api\Controllers;

use App\Admin\Models\Extend\ServerModel;
use App\Admin\Services\Extend\DatabaseService;
use App\Admin\Services\Extend\MachineService;
use App\Api\Models\ChargeOrderModel;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ChargeOrderController extends Controller
{
    private static string $PRODUCT_SECRET = '88c19f4cc4fb440f8996d771d34c3a3c';

    public function notify(Request $request)
    {
        // api.fake.me/payment?order_id=order_id&charge_id=1&channel=channel&role_id=1&role_name=role_name&server_id=1001&account_name=account_name&money=0&mark=mark&coupon=coupon&sign=a5a5af8edc03300e44aa8ba9ff96dcb3
        $time = time();

        // parameter
        $charge_id = $request->input('charge_id', 0);
        $order_id = $request->input('order_id', '');
        $channel_id = $request->input('channel_id', '');
        $role_id = $request->input('role_id', 0);
        $role_name = $request->input('role_name', '');
        $server_id = $request->input('server_id', 0);
        $money = $request->input('money', 0);
        $mark = $request->input('mark', '');
        $coupon = $request->input('coupon', '');
        $sign = $request->input('sign', '');

        // check sign
        if ($sign !== md5($order_id . $role_id . $money . $server_id . $mark . $coupon . self::$PRODUCT_SECRET)) {
            return [
                'code' => 0, 
                'msg' => 'Sign Not Matched'
            ];
        }

        // check server id
        $server = ServerModel::getServer($channel_id, $server_id);
    
        if (is_null($server)) {
            return [
                'code' => 0, 
                'msg' => "Cound not found server by channel_id: $channel_id and server_id: $server_id"
            ];
        }

        try {
            // connect to remote server database
            DatabaseService::changeConnection($server);
            
            // save charge order data
            $data = [
                'charge_id' => $charge_id,
                'order_id' => $order_id,
                'channel' => $channel_id,
                'role_id' => $role_id,
                'role_name' => $role_name,
                'money' => $money,
                'time' => $time
            ];
            $model = new ChargeOrderModel();
            $charge_no = $model->insertGetId($data);

            // notify server
            $data = [
                'charge_no' => $charge_no, 
                'role_id' => intval($role_id)
            ];
            $result = MachineService::send($server, 'charge', $data);
            
            if (isset($result['error'])) {
                Log::error('NOTIFY SERVER ERROR:', $result['error']);
            }

            return ['code' => 0, 'msg' => ''];
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            return ['code' => $exception->getCode(), 'msg' => $exception->getMessage()];
        }
    }
}
