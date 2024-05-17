<?php

namespace App\Admin\Builders\Form;

class Hidden extends Text
{
    /**
     * The attributes registered on the controller.
     * 
     * @var array
     */
    public $attributes = [
        'class' => ['layui-input'],
        'type' => 'hidden',
    ];
}
