<?php

namespace App\Admin\Builders\Form;

class Button extends Field
{
    /**
     * The attributes registered on the controller.
     * 
     * @var array
     */
    public $attributes = [
        'type' => 'button',
        'class' => ['layui-input', 'layui-btn-primary', 'layui-btn-normal'],
    ];

    /**
     * The name registered on the controller.
     * 
     * @var string
     */
    public $name = '';

    public function type(string $type): static
    {

        $this->attributes[__FUNCTION__] = $type;

        return $this;
    }

    public function flag(string $flag = 'primary'): static
    {
        $flag = 'layui-btn-' . $flag;

        if(in_array($flag, $this->attributes['class'])) {
            return $this;
        }

        $this->attributes['class'][] = $flag;

        return $this;
    }

    public function name(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function size(string $size = 'normal'): static
    {
        $size = 'layui-btn-' . $size;

        if(in_array($size, $this->attributes['class'])) {
            return $this;
        }

        $this->attributes['class'][] = $size;

        return $this;
    }

    public function render(): string
    {
        $fieldClass = $this->getFieldClass();

        $label = $this->formatLabel();

        $atttributes = $this->formatAttributes();

        return <<<HTML
<div class="{$fieldClass}">
    $label
    <div class="layui-input-block">
        <button {$atttributes} >{$this->name}</button>
    </div>
</div>

HTML;
    }
}
