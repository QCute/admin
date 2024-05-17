<?php

namespace App\Admin\Builders\Form;

class Month extends Text
{
    /**
     * The attributes registered on the controller.
     * 
     * @var array
     */
    public $attributes = [
        'class' => ['layui-input'],
    ];

    public function value(string $value): static
    {
        $this->attributes['value'] = $value;

        return $this;
    }

    public function run(): string
    {
        $selector = '[name="' . $this->attributes['name'] . '"]';

        $month = $this->attributes['value'] ?? '';

        return <<<JAVASCRIPT
            // render
            layui.laydate.render({
                elem: '{$selector}',
                value: '{$month}',
                type: 'month'
            });
JAVASCRIPT;
    }
}
