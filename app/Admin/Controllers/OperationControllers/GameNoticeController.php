<?php

namespace App\Admin\Controllers\OperationControllers;

use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Layout\Content;
use App\Admin\Forms\OperationForms\GameNoticeForm;

class GameNoticeController extends AdminController
{
    public function index(Content $content): Content
    {
        return $content->title("")->body(new GameNoticeForm());
    }
}
