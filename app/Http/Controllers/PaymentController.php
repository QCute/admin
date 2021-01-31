<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use App\Admin\Controllers\SwitchServerController;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    private static $PRODUCT_SECRET = "88c19f4cc4fb440f8996d771d34c3a3c";
    public function pay()
    {
        // api.fake.me/payment?order_id=order_id&recharge_id=1&channel=channel&role_id=1&role_name=role_name&server_id=1001&account_name=account_name&money=0&mark=mark&coupon=coupon&sign=a5a5af8edc03300e44aa8ba9ff96dcb3
        $time = time();
        // parameter
        $order_id = request()->input("order_id", "");
        $recharge_id = request()->input("recharge_id", 0);
        $channel = request()->input("channel", "");
        $role_id = request()->input("role_id", 0);
        $role_name = request()->input("role_name", "");
        $server_id = request()->input("server_id", 0);
        $account_name = request()->input("account_name", "");
        $money = request()->input("money", 0);
        $mark = request()->input("mark", "");
        $coupon = request()->input("coupon", "");
        $sign = request()->input("sign", "");
        // check sign
        if ($sign !== md5($order_id . $role_id . $money . $server_id . $mark . $coupon . self::$PRODUCT_SECRET)) {
            return json_encode(["status" => "failure", "code" => 0, "msg" => "Sign Not Matched"]);
        }
        // check server id
        $server = SwitchServerController::getServer($server_id);
        if (is_null($server)) {
            return json_encode(["status" => "failure", "code" => 0, "msg" => "Server Id Invalid"]);
        }
        // save recharge data
        try {
            $data = ["order_id" => $order_id, "recharge_id" => $recharge_id, "channel" => $channel, "role_id" => $role_id, "role_name" => $role_name, "server_id" => $server_id, "account_name" => $account_name, "money" => $money, "time" => $time];
            $recharge_no = DB::insert("INSERT INTO `{$server->server_node}`.`recharge` (`order_id`, `recharge_id`, `channel`, `role_id`, `role_name`, `server_id`, `account_name`, `money`, `time`) VALUES (:order_id, :recharge_id, :channel, :server_id, :role_id, :role_name, :account_name, :money, :time)", $data);
            // notify server
            SwitchServerController::send($server_id, "recharge", json_encode(["recharge_no" => $recharge_no, "role_id" => $role_id]));
            return json_encode(["status" => "success", "code" => 0, "msg" => ""]);
        } catch (QueryException $exception) {
            Log::error($exception->getMessage());
            return json_encode(["status" => "success", "code" => 0, "msg" => ""]);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return json_encode(["status" => "failure", "code" => 0, "msg" => "Duplicated OrderId"]);
        }
    }
}
