<?php

namespace App\Admin\Builders\Form;

class DateTime extends Text
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

        $dateTime = $this->attributes['value'] ?? '';

        return <<<JAVASCRIPT
            // render
            layui.laydate.render({
                elem: '{$selector}',
                value: '{$dateTime}',
                type: 'datetime'
            });
JAVASCRIPT;
    }
}
