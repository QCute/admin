<?php

namespace App\Admin\Builders\Form;

class Logo extends Field
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
        'style' => [
            'width' => '38px',
            'height' => '38px',
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

        $src = empty($this->value) ? '' : 'src="' . $this->value . '"';

        return <<<HTML
<div class="{$fieldClass}">
    $label
    <div class="layui-input-block">
        <img id="logo-{$name}" {$src} $attributes>
    </div>
</div>
HTML;
    }
}
