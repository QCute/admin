<?php

namespace App\Http\Controllers;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use App\Admin\Controllers\SwitchServerController;

class PaymentController extends Controller
{
    private static $PRODUCT_SECRET = "88c19f4cc4fb440f8996d771d34c3a3c";
    public function pay()
    {
        // api.fake.me?price=500&callbackInfo=e3VzZXJJZDoxMjMsb3JkZXJJZDo3ODkwMTIzMTJ9&orderId=8efnc2&channelCode=a20dcb1c683b46339b82e6b056aa44cc&channelLabel=wandoujia&channelOrderId=201601290990900000012&sign=64ba7cd68b635c0c8ce3eca04252be73&freePrice=100&version=v2.0&sdkCode=wandoujia&sign2=9d8520ee868b68dc4c377f51377a453b
        $parameter = request()->all();
        Arr::pull($parameter, 'sign');
        $sign2 = Arr::pull($parameter, 'sign2');
        ksort($parameter);
        $string = implode("&", array_map(function($key, $value) { return $key . "=" . $value; }, array_keys($parameter), array_values($parameter))) . "&" .  self::$PRODUCT_SECRET;
        if (md5($string) != $sign2) return "fail";
        // {userId: 123, orderId: 789012312, ...}
        // json key must be contain string quote
        $info = json_decode(base64_decode($parameter["callbackInfo"]), true);
        try {
            $recharge_no = DB::insert("INSERT INTO (`recharge_id`, `channel`, `server_id`, `role_id`, `role_name`, `account`, `money`, `time`) VALUES (" . implode("',", array($info["recharge_id"], $info["channel"], $info["server_id"], $info["role_id"], $info["role_name"], $info["account"], $parameter["price"] / 100, time())) . ")");
        } catch (\Exception $exception) {
            return "fail";
        }
        SwitchServerController::send("", "recharge", json_encode(array("recharge_no" => $recharge_no, "role_id" => $info["userId"])));
        return "true";
    }
}
