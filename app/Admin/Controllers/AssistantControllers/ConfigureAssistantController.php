<?php

namespace App\Admin\Controllers\AssistantControllers;

use App\Admin\Controllers\SwitchServerController;
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
        // check
        $server = SwitchServerController::getCurrentServerNode();
        if (empty($server)) {
            return $content
                ->title($this->title())
                ->withWarning("Could not found current server");
        }
        // view
        return $content->title("")->body(new ConfigureAssistantForm());
    }
}
