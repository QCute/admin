<?php

namespace App\Admin\Builders\Form;

class Transfer extends Field
{
    /**
     * The left registered on the controller.
     * 
     * @var array<TransferOption>
     */
    public $left;

    /**
     * The left title registered on the controller.
     * 
     * @var string
     */
    public $leftTitle;

    /**
     * The right registered on the controller.
     * 
     * @var array<TransferOption>
     */
    public $right;

    /**
     * The right title registered on the controller.
     * 
     * @var string
     */
    public $rightTitle;

    /**
     * The attributes registered on the controller.
     * 
     * @var array
     */
    public $attributes = [

    ];
    
    public function title(string $left, string $right): static
    {
        $this->leftTitle = $left;

        $this->rightTitle = $right;

        return $this;
    }

    public function left(): TransferOption
    {
        $name = $this->attributes['name'] . '[]';

        $field = new TransferOption($this->form, $name);

        // $field->attributes['checked'] = false;

        return tap($field, function ($field) {
            $this->left[] = $field;
        });
    }

    public function right(): TransferOption
    {
        $name = $this->attributes['name'] . '[]';
        
        $field = new TransferOption($this->form, $name);

        // $field->attributes['checked'] = true;

        return tap($field, function ($field) {
            $this->right[] = $field;
        });
    }

    public function render(): string
    {
        $fieldClass = $this->getFieldClass();

        $label = $this->formatLabel();

        $name = $this->attributes['name'];

        $view = collect($this->right)
            ->map(function($item) {
                return $item->render();
            })
            ->implode("\n");

        return <<<HTML
<div class="{$fieldClass}">
    $label
    <div class="layui-hidden" name="transfer-wrap-{$name}">
        $view
    </div>
    <div name="transfer-{$name}">
    </div>
</div>
HTML;
    }

    public function run(): string
    {
        $name = $this->attributes['name'];

        $title = collect([$this->leftTitle, $this->rightTitle])
            ->map(function($value) { 
                return trim($value); 
            })
            ->toJson();

        $data = collect($this->left)
            ->merge($this->right)
            ->map(function($item) {
                return [
                    'value' => $item->attributes['value'] ?? '', 
                    'title' => $item->attributes['title'] ?? '', 
                    'disabled' => $item->attributes['disabled'] ?? false, 
                ];
            })
            ->toJson();

        $value = collect($this->right)
            ->map(function($item) {
                return $item->attributes['value'] ?? '';
            })
            ->toJson();

        return <<<JAVASCRIPT
layui.transfer.render({
    elem: '[name="transfer-{$name}"]',
    title: {$title},
    data: {$data},
    value: {$value},
    onchange: function(list, from) {
        const children = this.value.map(value => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = '{$name}[]';
            input.value = value;
            return input;
        });
        document.querySelector('[name="transfer-wrap-{$name}"]').replaceChildren(...children);
        return;
        list.forEach(element => {
            document.querySelector('[name="transfer-{$name}"]').querySelector('[value="' + element.value + '"]').checked = (from == 0);
        });
    }
});
JAVASCRIPT;
    }
}
