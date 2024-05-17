<?php

namespace App\Admin\Builders\Form;

class Tags extends Field
{
    /**
     * The options registered on the controller.
     * 
     * @var array<Tag>
     */
    public $tags;

    /**
     * The attributes registered on the controller.
     * 
     * @var array
     */
    public $attributes = [
    ];

    public function tag(): Tag
    {
        $name = $this->attributes['name'];

        $field = new Tag($this->form, $name . '[]');

        return tap($field, function ($field) {
            $this->tags[] = $field;
        });
    }

    public function render(): string
    {
        $fieldClass = $this->getFieldClass();

        $label = $this->formatLabel();

        $view = collect($this->tags)
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
}
