<?php

namespace App\Admin\Controllers\AssistantControllers;

use App\Admin\Forms\AssistantForms\KeyAssistantForm;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Layout\Content;

class KeyAssistantController extends AdminController
{
    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content): Content
    {
        return $content->title("")->body(new KeyAssistantForm($content));
    }
}
