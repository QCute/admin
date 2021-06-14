<?php

namespace App\Admin\Controllers\AssistantControllers;

use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Layout\Content;
use App\Admin\Forms\AssistantForms\KeyAssistantForm;

class KeyAssistantController extends AdminController
{
    public function index(Content $content): Content
    {
        return $content->title("")->body(new KeyAssistantForm($content));
    }
}
