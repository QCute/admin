<?php

namespace App\Admin\Builders\Form;

class Password extends Text
{
    /**
     * The attributes registered on the controller.
     * 
     * @var array
     */
    public $attributes = [
        'class' => ['layui-input'],
        'type' => 'password',
        'lay-affix' => "eye"
    ];
}
