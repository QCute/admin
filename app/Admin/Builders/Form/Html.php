<?php

namespace App\Admin\Builders\Form;

class Html extends Field
{
    public function render(): string
    {
        $label = $this->formatLabel();

        return <<<HTML
<div class="layui-form-item layui-{$this->getDisplay()}">
    $label
    <div class="layui-{$this->getDisplay()}">
        {$this->value}
    </div>
</div>
HTML;
    }
}
