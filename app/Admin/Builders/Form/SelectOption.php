<?php

namespace App\Admin\Builders\Form;

class SelectOption extends Field
{
    public function value(string $value): static
    {
        $this->attributes['value'] = $value;

        return $this;
    }

    public function select(bool $selected = true): static
    {
        if(!$selected) {
            unset($this->attributes['selected']);
        } else {
            $this->attributes['selected'] = 'selected';
        }

        return $this;
    }

    public function render(): string
    {
        $attributes = $this->formatAttributes();

        return <<<HTML
<option $attributes>{$this->label}</option>
HTML;
    }
}
