<?php

namespace App\Admin\Builders\Form;

class Select extends Field
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
     * The options registered on the controller.
     * 
     * @var array<SelectOption>
     */
    public $options;

    /**
     * The attributes registered on the controller.
     * 
     * @var array
     */
    public $attributes = [
        'class' => ['layui-input'],
        'lay-search' => '',
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

    public function option(): SelectOption
    {
        $field = new SelectOption($this->form, '');

        return tap($field, function ($field) {
            $this->options[] = $field;
        });
    }

    public function render(): string
    {
        $label = $this->formatLabel();

        $attributes = $this->formatAttributes();

        $options = collect($this->options)
            ->map(function($item) {
                return $item->render();
            })
            ->implode("\n");

        if($this->prefix === '') {
            return <<<HTML
<div class="layui-form-item layui-{$this->getDisplay()}">
    $label
    <div class="layui-input-{$this->getDisplay()}">
        <select $attributes>
            {$options}
        </select>
    </div>
</div>

HTML;
        }

        return <<<HTML
<div class="layui-form-item layui-{$this->getDisplay()}">
    $label
    <div class="layui-input-{$this->getDisplay()}">
        <div class="layui-input-wrap">
            <div class="layui-input-prefix">
                <i class="{$this->prefix}"></i>
            </div>
            <select $attributes>
                {$options}
            </select>
        </div>
    </div>
</div>

HTML;
    }
}
