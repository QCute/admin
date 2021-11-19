<?php

namespace App\Admin\Controllers\AssistantControllers;

use App\Admin\Forms\AssistantForms\ConfigureAssistantForm;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Layout\Content;

class ConfigureAssistantController extends AdminController
{
    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content): Content
    {
        return $content->title("")->body(new ConfigureAssistantForm());
    }
}
