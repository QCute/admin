<?php

namespace App\Admin\Controllers\OperationControllers;

use App\Admin\Forms\OperationForms\GameMailForm;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Layout\Content;

class GameMailController extends AdminController
{
    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content): Content
    {
        return $content->title("")->body(new GameMailForm());
    }
}
