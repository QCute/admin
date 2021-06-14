<?php

namespace App\Admin\Controllers\ServerManageControllers;

use App\Http\Controllers\Controller;
use Encore\Admin\Layout\Content;
use App\Admin\Forms\ServerManageForms\MergeServerForm;

class MergeServerController extends Controller
{
    public function index(Content $content): Content
    {
        return $content->title("")->body(new MergeServerForm());
    }
}
