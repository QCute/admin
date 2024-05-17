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

    public function value(string $value): static
    {
        $this->attributes[__FUNCTION__] = $value;

        return $this;
    }
}
