<?php

namespace App\Admin\Builders\Table;

use JsonSerializable;

class Action implements JsonSerializable
{
    /** 
     * The title registered on the controller.
     * 
     * @var string
     */
    public $title = '';

    /** 
     * The title registered on the controller.
     * 
     * @var string
     */
    public $icon = '';

    /** 
     * The color registered on the controller.
     * 
     * @var string
     */
    public $color = '';

    /** 
     * The event registered on the controller.
     * 
     * @var string
     */
    public $event = '';

    /** 
     * The value registered on the controller.
     * 
     * @var string
     */
    public $value = '';

    public function title(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function icon(string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function color(string $color): static
    {
        $this->color = $color;

        return $this;
    }

    public function event(string $event): static
    {
        $this->event = $event;

        return $this;
    }

    public function value(string $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function jsonSerialize(): array 
    {
        return (array)$this;
    }
}
