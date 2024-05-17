<?php

namespace App\Admin\Builders\Form;

use App\Admin\Builders\Form;

class RadioOption extends Field
{
    /**
     * The attributes registered on the controller.
     * 
     * @var array
     */
    public $attributes = [
        'type' => 'radio',
    ];

    public function __construct(Form $form, string $name)
    {
        $this->form = $form;
        $this->attributes['name'] = $name;
        $this->attributes['lay-verify'] = $name . 'Verify';
    }

    public function label(string $label): static
    {
        $this->attributes['title'] = $label;

        return $this;
    }

    public function value(string $value): static
    {
        $this->attributes['value'] = $value;

        return $this;
    }

    public function check(bool $checked = true): static
    {
        if(!$checked) {
            unset($this->attributes['checked']);
        } else {
            $this->attributes['checked'] = 'checked';
        }

        return $this;
    }

    public function render(): string
    {
        $attributes = $this->formatAttributes();

        return <<<HTML
<input $attributes />
HTML;
    }
}
