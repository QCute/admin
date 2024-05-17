<?php

namespace App\Admin\Builders\Form;
use App\Admin\Builders\Form;

class SwitchBox extends Field
{
    /**
     * The attributes registered on the controller.
     * 
     * @var array
     */
    public $attributes = [
        'class' => ['layui-input'],
        'type' => 'checkbox',
        'lay-skin' => 'switch',
        'title' => 'ON|OFF',
    ];

    public function __construct(Form $form, string $name)
    {
        $this->attributes['lay-filter'] = 'switch-' . $name;

        parent::__construct($form, $name);
    }

    public function value(string $value): static
    {
        $value = strtolower($value);

        $this->attributes['checked'] = $value == 'on' ? true : false;

        return $this;
    }

    public function run(): string
    {
        $name = $this->attributes['name'];
        return <<<JAVASCRIPT
layui.form.on('switch(switch-{$name})', function(data) {
    // const element = document.getElementsByName('{$name}')[0];
    // console.log(element.checked);
    // element.checked != element.checked;
});
JAVASCRIPT;
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
        <input $attributes />
    </div>
</div>
HTML;
    }
}
