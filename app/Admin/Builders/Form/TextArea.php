<?php

namespace App\Admin\Builders\Form;

class TextArea extends Field
{
    /**
     * The attributes registered on the controller.
     * 
     * @var array
     */
    public $attributes = [
        'class' => ['layui-textarea'],
    ];

    public function render(): string
    {
        $label = $this->formatLabel();

        $attributes = $this->formatAttributes();

        return <<<HTML
<div class="layui-form-item layui-{$this->getDisplay()}">
    $label
    <div class="layui-input-{$this->getDisplay()}">
        <textarea $attributes>{$this->value}</textarea>
    </div>
</div>
HTML;
    }
}
