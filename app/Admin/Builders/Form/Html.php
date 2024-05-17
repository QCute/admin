<?php

namespace App\Admin\Builders\Form;

class Html extends Field
{
    /** 
     * The value registered on the controller.
     * 
     * @var string
     */
    public $value = '';

    public function value(string $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function render(): string
    {
        $fieldClass = $this->getFieldClass();

        $label = $this->formatLabel();

        return <<<HTML
<div class="{$fieldClass}">
    $label
    <div class="layui-input-block">
        {$this->value}
    </div>
</div>
HTML;
    }
}
