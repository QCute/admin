<?php

namespace App\Admin\Controllers\GameConfigureControllers;

use Illuminate\Support\Facades\DB;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use App\Admin\Controllers\SwitchServerController;

class ConfigureAssistantController extends Controller
{
    public function index(Content $content)
    {
        return $content;
    }
}
