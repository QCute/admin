<?php

namespace App\Admin\Controllers\ServerManageControllers;

use App\Http\Controllers\Controller;
use App\Admin\Forms\ServerManageForms\OpenServerForm;
use Encore\Admin\Layout\Content;

class OpenServerController extends Controller
{
    public function index(Content $content): Content
    {
        return $content->title("")->body(new OpenServerForm());
    }
}
