<?php

namespace App\Http\Controllers;

use App\Admin\Controllers\SwitchServerController;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    private static string $PRODUCT_SECRET = "88c19f4cc4fb440f8996d771d34c3a3c";
    public function pay(): JsonResponse
    {
        // api.fake.me/payment?order_id=order_id&recharge_id=1&channel=channel&role_id=1&role_name=role_name&server_id=1001&account_name=account_name&money=0&mark=mark&coupon=coupon&sign=a5a5af8edc03300e44aa8ba9ff96dcb3
        $time = time();
        // parameter
        $recharge_id = request()->input("recharge_id", 0);
        $order_id = request()->input("order_id", "");
        $channel = request()->input("channel", "");
        $role_id = request()->input("role_id", 0);
        $role_name = request()->input("role_name", "");
        $server_id = request()->input("server_id", 0);
        $money = request()->input("money", 0);
        $mark = request()->input("mark", "");
        $coupon = request()->input("coupon", "");
        $sign = request()->input("sign", "");
        // check sign
        if ($sign !== md5($order_id . $role_id . $money . $server_id . $mark . $coupon . self::$PRODUCT_SECRET)) {
            return response()->json(["status" => "failure", "code" => 0, "msg" => "Sign Not Matched"]);
        }
        // check server id
        $server = SwitchServerController::getServer($server_id);
        if (is_null($server)) {
            return response()->json(["status" => "failure", "code" => 0, "msg" => "Server Id Invalid"]);
        }
        // save recharge data
        try {
            $connection = SwitchServerController::changeConnection($server);
            $data = [
                "recharge_id" => $recharge_id,
                "order_id" => $order_id,
                "channel" => $channel,
                "role_id" => $role_id,
                "role_name" => $role_name,
                "money" => $money,
                "time" => $time
            ];
            $recharge_no = $connection->table("`recharge`")->insertGetId($data);
            // notify server
            $result = SwitchServerController::send($server, "recharge", ["recharge_no" => $recharge_no, "role_id" => intval($role_id)]);
            if (!empty($result["error"])) {
                Log::error("NOTIFY SERVER ERROR:", $result["error"]);
            }
            return response()->json(["status" => "success", "code" => 0, "msg" => ""]);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            return response()->json(["status" => "failure", "code" => $exception->getCode(), "msg" => $exception->getMessage()]);
        }
    }
}
