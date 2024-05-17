<?php

namespace App\Admin\Builders\Form;

class Radio extends Field
{
    /**
     * The options registered on the controller.
     * 
     * @var array<RadioOption>
     */
    public $options;

    /**
     * The attributes registered on the controller.
     * 
     * @var array
     */
    public $attributes = [
        'class' => ['layui-input'],
    ];

    public function option(): RadioOption
    {
        $name = $this->attributes['name'];

        $field = new RadioOption($this->form, $name);

        return tap($field, function ($field) {
            $this->options[] = $field;
        });
    }

    public function render(): string
    {
        $fieldClass = $this->getFieldClass();

        $label = $this->formatLabel();

        $view = collect($this->options)
            ->map(function($item) {
                return $item->render();
            })
            ->implode("\n");

        return <<<HTML
<div class="{$fieldClass}">
    $label
    <div class="layui-input-block">
        $view
    </div>
</div>
HTML;
    }

    public function run(): string
    {
        $name = $this->attributes['name'];

        if(!collect($this->attributes['lay-verify'] ?? [])->contains('required')) {
            return '';
        }

        return <<<JAVASCRIPT
layui.form.verify({
    {$name}Verify: function(value, element) {
        const isChecked = Array.from(element.parentNode.querySelectorAll('[name="' + element.name + '"]')).reduce((acc, item) => item.checked || acc, false);
        if(isChecked) return undefined;
        // find icon view
        const list = element.parentNode.querySelectorAll('i.layui-icon');
        const focusStyle = { "color": "#FF5722" };
        // focus
        for(const item of list) {
            for(const key in focusStyle) {
                item.style[key] = focusStyle[key];
            }
        }
        // first
        list[0].setAttribute("tabIndex", "1");
        list[0].style.outline = "0";
        // blur
        const blurStyle = { "color": "" };
        list[0].addEventListener('blur', () => {
            for(const item of list) {
                for(const key in blurStyle) {
                    item.style[key] = blurStyle[key];
                }
            }
        });
        list[0].focus();

        return layui.form.config.verify.required('');
    }
});
JAVASCRIPT;
    }
}
