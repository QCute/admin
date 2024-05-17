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

    public function value(string $value): static
    {
        $this->attributes[__FUNCTION__] = $value;

        return $this;
    }
}
