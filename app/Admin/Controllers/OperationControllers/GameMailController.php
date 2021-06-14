<?php

namespace App\Admin\Controllers\OperationControllers;

use Encore\Admin\Form;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use App\Admin\Forms\OperationForms\GameMailForm;
use App\Admin\Controllers\SwitchServerController;

class GameMailController extends Controller
{
    public function index(Content $content): Content
    {
        return $content->title("")->body(new GameMailForm());
    }
}
