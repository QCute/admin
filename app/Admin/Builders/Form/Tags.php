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
        $label = $this->formatLabel();

        $view = collect($this->tags)
            ->map(function($item) {
                return $item->render();
            })
            ->implode("\n");

        return <<<HTML
<div class="layui-form-item layui-{$this->getDisplay()}">
    $label
    <div class="layui-input-{$this->getDisplay()}">
        $view
    </div>
</div>
HTML;
    }
}
