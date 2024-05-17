<?php

namespace App\Admin\Builders\Form;

class Image extends Field
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
   
        $label = $this->formatLabel();
        
        $attributes = $this->formatAttributes();
        
        $name = $this->attributes['name'];

        $choose = trans('admin.form.choose');

        $src = empty($this->value) ? '' : 'src="' . $this->value . '"';

        return <<<HTML
<div class="{$fieldClass}">
    $label
    <div class="layui-input-block" style="width: 160px;">
        <div class="layui-upload-list">
            <img id="img-{$name}" class="layui-upload-img" {$src} style="width: 160px; height: 160px;">
        </div>
        <button type="button" class="layui-btn" style="position: relative;">
            <input $attributes />
            <i class="layui-icon layui-icon-upload"></i>{$choose}
        </button>
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
    accept: 'images',
    choose: function(object) {
        object.preview(function(index, file, result) {
            const reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = () => {
                document.querySelector("#img-{$name}").src = reader.result;
            };
        });
    }
});
JAVASCRIPT;
    }
}
