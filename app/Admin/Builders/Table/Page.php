<?php

namespace App\Admin\Builders\Table;

use JsonSerializable;

class Page implements JsonSerializable
{
    /** 
     * The total registered on the controller.
     * 
     * @var int
     */
    public $total;

    /** 
     * The current registered on the controller.
     * 
     * @var int
     */
    public $current;

    /** 
     * The limit registered on the controller.
     * 
     * @var int
     */
    public $limit;

    /** 
     * The limits registered on the controller.
     * 
     * @var array
     */
    public $limits = [10, 20, 30, 40, 50, 60, 70, 80, 90, 100];

    /** 
     * The layout registered on the controller.
     * 
     * @var array
     */
    public $layout = ['count', 'prev', 'page', 'next', 'limit', 'refresh', 'skip'];

    public function total(int $total): static
    {
        $this->total = $total;

        return $this;
    }

    public function current(int $current): static
    {
        $this->current = $current;

        return $this;
    }

    public function limit(string $limit): static
    {
        $this->limit = $limit;

        return $this;
    }

    public function limits(array $limits): static
    {
        $this->limits = $limits;

        return $this;
    }

    public function layout(array $layout): static
    {
        $this->layout = $layout;

        return $this;
    }

    public function jsonSerialize(): array 
    {
        return (array)$this;
    }
}
