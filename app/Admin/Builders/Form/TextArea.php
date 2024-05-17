<?php

namespace App\Admin\Builders\Form;

class TextArea extends Field
{
    /**
     * The value registered on the controller.
     * 
     * @var string
     */
    public $value = '';

    /**
     * The attributes registered on the controller.
     * 
     * @var array
     */
    public $attributes = [
        'class' => ['layui-textarea'],
    ];

    public function value(string $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function render(): string
    {
        $fieldClass = $this->getFieldClass();

        $label = $this->formatLabel();

        $attributes = $this->formatAttributes();

        return <<<HTML
<div class="{$fieldClass}">
    $label
    <div class="layui-input-block">
        <textarea $attributes>{$this->value}</textarea>
    </div>
</div>
HTML;
    }
}
