<?php

namespace App\Admin\Controllers\ServerManageControllers;

use App\Admin\Forms\ServerManageForms\MergeServerForm;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Layout\Content;

class MergeServerController extends AdminController
{
    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content): Content
    {
        return $content->title("")->body(new MergeServerForm());
    }
}
