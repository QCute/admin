<?php

namespace App\Admin\Builders\Form;

class File extends Field
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
        'type' => 'file',
        'style' => [
            'width' => '100%',
            'position' => 'absolute',
            'height' => '100%',
            'top' => 0,
            'left' => 0,
            'opacity' => 0,
            'cursor' => 'pointer',
        ],
    ];

    public function value(string $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function render(): string
    {
        $fieldClass = $this->getFieldClass();

        $name = $this->attributes['name'];

        $choose = $this->value ?? trans('admin.form.choose');

        $label = $this->formatLabel();

        $attributes = $this->formatAttributes();

        return <<<HTML
<div class="{$fieldClass}">
    $label
    <div class="layui-input-block layui-upload-drag" style="display: block;">
        <input $attributes />
        <i class="layui-icon layui-icon-upload"></i> 
        <div id="file-text-{$name}">{$choose}</div>
    </div>
</div>
HTML;
    }

    public function run(): string
    {
        $name = $this->attributes['name'];

        return <<<JAVASCRIPT
layui.upload.render({
    elem: '[name="{$name}"]',
    auto: false,
    accept: 'file',
    choose: function(object) {
        object.preview(function(index, file, result) {
            document.querySelector("#file-text-{$name}").innerText = file.name;
        });
    }
});

JAVASCRIPT;
    }
}
