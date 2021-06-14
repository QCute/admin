<?php

namespace App\Admin\Controllers\AssistantControllers;

use App\Http\Controllers\Controller;
use Encore\Admin\Layout\Content;
use App\Admin\Forms\AssistantForms\ConfigureAssistantForm;

class ConfigureAssistantController extends Controller
{
    public function index(Content $content): Content
    {
        return $content->title("")->body(new ConfigureAssistantForm());
    }
}
