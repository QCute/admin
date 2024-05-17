<?php

namespace App\Admin\Builders\Table;

use JsonSerializable;

class Header implements JsonSerializable
{
    /** 
     * The field registered on the controller.
     * 
     * @var string
     */
    public $field;

    /** 
     * The title registered on the controller.
     * 
     * @var string
     */
    public $title;

    /** 
     * The templet registered on the controller.
     * 
     * @var string
     */
    public $templet;

    /** 
     * The templet schema registered on the controller.
     * 
     * @var string
     */
    public $templetSchema;

    /** 
     * The align registered on the controller.
     * 
     * @var string
     */
    public $align;

    /** 
     * The width registered on the controller.
     * 
     * @var int
     */
    public $width;

    /** 
     * The minWidth registered on the controller.
     * 
     * @var int
     */
    public $minWidth;

    /** 
     * The height registered on the controller.
     * 
     * @var int
     */
    public $height;

    /** 
     * The minHeight registered on the controller.
     * 
     * @var int
     */
    public $minHeight;

    /** 
     * The sort registered on the controller.
     * 
     * @var bool
     */
    public $sort;

    /** 
     * The toolbar registered on the controller.
     * 
     * @var string
     */
    public $toolbar;

    public function field(string $field): static
    {
        $this->field = $field;

        return $this;
    }

    public function title(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function templet(string $templet, string $schema = ''): static
    {
        $this->templet = '#' . $templet;

        $this->templetSchema = $schema;

        return $this;
    }

    public function align(string $align = 'center'): static
    {
        $this->align = $align;

        return $this;
    }

    public function width(int $width): static
    {
        $this->width = $width;

        return $this;
    }

    public function minWidth(int $width): static
    {
        $this->minWidth = $width;

        return $this;
    }

    public function height(int $height): static
    {
        $this->height = $height;

        return $this;
    }

    public function minHeight(int $height): static
    {
        $this->minHeight = $height;

        return $this;
    }

    public function sort(bool $sort = false): static
    {
        $this->sort = $sort;

        return $this;
    }

    public function toolbar(string $toolbar = '#table-operation'): static
    {
        $this->toolbar = $toolbar;

        return $this;
    }

    public function jsonSerialize(): array 
    {
        return (array)$this;
    }
}
