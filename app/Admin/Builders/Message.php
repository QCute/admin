<?php

namespace App\Admin\Builders;

use Illuminate\Support\MessageBag;

class Message
{
    public static function success($title, $message = '')
    {
        $message = new MessageBag(get_defined_vars());

        session()->now(__FUNCTION__, $message);
    }

    public static function info($title, $message = '')
    {
        $message = new MessageBag(get_defined_vars());

        session()->now(__FUNCTION__, $message);
    }

    public static function warning($title, $message = '')
    {
        $message = new MessageBag(get_defined_vars());

        session()->now(__FUNCTION__, $message);
    }

    public static function error($title, $message = '')
    {
        $message = new MessageBag(get_defined_vars());

        session()->now(__FUNCTION__, $message);
    }
}
