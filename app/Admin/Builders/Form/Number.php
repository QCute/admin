<?php

namespace App\Admin\Builders\Form;

class Number extends Text
{
    /**
     * The attributes registered on the controller.
     * 
     * @var array
     */
    public $attributes = [
        'class' => ['layui-input'],
        'type' => 'number',
        'lay-affix' => "number"
    ];

    public function value(string $value): static
    {
        $this->attributes[__FUNCTION__] = $value;

        return $this;
    }

    public function min(int $min): static
    {
        $this->attributes[__FUNCTION__] = $min;

        return $this;
    }

    public function step(int $step): static
    {
        $this->attributes[__FUNCTION__] = $step;

        return $this;
    }

    public function max(int $max): static
    {
        $this->attributes[__FUNCTION__] = $max;

        return $this;
    }
}
