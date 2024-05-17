<?php

namespace App\Admin\Builders\Form;

class Tag extends Field
{
    /**
     * The attributes registered on the controller.
     * 
     * @var array
     */
    public $attributes = [
        'type' => 'button',
        'class' => ['layui-btn', 'layui-btn-xs', 'layui-bg-blue'],
        'style' => [
            'margin-top' => '8px'
        ]
    ];

    public function color(string $color = 'blue'): static
    {
        $color = 'layui-btn-' . $color;

        if(in_array($color, $this->attributes['class'])) {
            return $this;
        }

        $this->attributes['class'][] = $color;

        return $this;
    }

    public function render(): string
    {
        $atttributes = $this->formatAttributes();

        return <<<HTML

    <button {$atttributes}>{$this->value}</button>

HTML;
    }
}
