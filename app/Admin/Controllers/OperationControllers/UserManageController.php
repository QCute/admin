<?php

namespace App\Admin\Controllers\OperationControllers;

use App\Admin\Forms\OperationForms\UserManageForm;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Layout\Content;

class UserManageController extends AdminController
{
    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content): Content
    {
        return $content->title("")->body(new UserManageForm());
    }
}
