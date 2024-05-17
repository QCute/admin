<?php

namespace App\Admin\Builders\Form;

class Text extends Field
{
    /**
     * The prefix registered on the controller.
     * 
     * @var string
     */
    public $prefix = '';

    /**
     * The suffix registered on the controller.
     * 
     * @var string
     */
    public $suffix = '';

    /**
     * The attributes registered on the controller.
     * 
     * @var array
     */
    public $attributes = [
        'class' => ['layui-input'],
        'type' => 'text',
    ];

    public function prefix(string $prefix): static
    {
        $this->prefix = $prefix;

        return $this;
    }

    public function suffix(string $suffix): static
    {
        $this->attributes['lay-affix'] = $suffix;

        return $this;
    }

    public function value(string $value): static
    {
        $this->attributes[__FUNCTION__] = $value;

        return $this;
    }

    public function placeholder(string $placeholder): static
    {
        $this->attributes[__FUNCTION__] = $placeholder;

        return $this;
    }

    public function render(): string
    {
        $fieldClass = $this->getFieldClass();

        $label = $this->formatLabel();

        $attributes = $this->formatAttributes();

        if($this->prefix === '') {
            return <<<HTML
<div class="{$fieldClass}">
    $label
    <div class="layui-input-block">
        <input $attributes />
    </div>
</div>
HTML;
        }

        return <<<HTML
<div class="{$fieldClass}">
    $label
    <div class="layui-input-block layui-input-wrap">
        <div class="layui-input-prefix">
            <i class="{$this->prefix}"></i>
        </div>
        <div class="layui-input-block">
            <input $attributes />
        </div>
    </div>
</div>
HTML;
    }
}
