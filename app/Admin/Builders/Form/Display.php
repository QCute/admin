<?php

namespace App\Admin\Builders\Form;

class Display extends Text
{
    /**
     * The attributes registered on the controller.
     * 
     * @var array
     */
    public $attributes = [
        'class' => ['layui-input'],
        'type' => 'text',
        'disabled' => true
    ];
}
