<?php

namespace App\Admin\Builders\Form;

class TransferOption extends Field
{
    /**
     * The attributes registered on the controller.
     * 
     * @var array
     */
    public $attributes = [
        'type' => 'hidden'
    ];

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

    public function render(): string
    {
        $attributes = $this->formatAttributes();

        return <<<HTML
<input $attributes />
HTML;
    }
}
