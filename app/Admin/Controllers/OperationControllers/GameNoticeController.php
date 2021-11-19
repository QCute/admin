<?php

namespace App\Admin\Controllers\OperationControllers;

use App\Admin\Forms\OperationForms\GameNoticeForm;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Layout\Content;

class GameNoticeController extends AdminController
{
    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content): Content
    {
        return $content->title("")->body(new GameNoticeForm());
    }
}
