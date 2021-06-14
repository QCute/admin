<?php

namespace App\Admin\Controllers\OperationControllers;

use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use App\Admin\Forms\OperationForms\UserManageForm;

class UserManageController extends Controller
{
    public function index(Content $content): Content
    {
        return $content->title("")->body(new UserManageForm());
    }
}
