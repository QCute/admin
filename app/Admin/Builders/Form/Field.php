<?php

namespace App\Admin\Builders\Form;

use App\Admin\Builders\Form;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Traits\Macroable;

abstract class Field implements Renderable
{
    use Macroable;

    /** 
     * The form registered on the controller.
     * 
     * @var Form
     */
    public $form;

    /** 
     * The label registered on the controller.
     * 
     * @var string
     */
    public $label = '';

    /**
     * The inline registered on the controller.
     *
     * @var bool
     */
    public $inline = false;

    /**
     * The attributes registered on the controller.
     * 
     * @var array
     */
    public $attributes = [];

    public function __construct(Form $form, string $name)
    {
        $this->form = $form;
        $this->attributes['name'] = $name;
    }

    public function id(string $id): static
    {
        $this->attributes[__FUNCTION__] = $id;

        return $this;
    }

    public function class(array|Arrayable $class): static
    {
        $this->attributes[__FUNCTION__] = collect($this->attributes[__FUNCTION__] ?? [])->merge($class);

        return $this;
    }

    public function required(): static
    {
        return $this->rules([__FUNCTION__]);
    }

    public function rules(array|Arrayable $rules): static
    {
        $this->attributes['lay-verify'] = collect($this->attributes['lay-verify'] ?? [])->merge($rules);

        return $this;
    }

    public function label(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function inline(bool $inline = true): static
    {
        $this->inline = $inline;

        return $this;
    }

    public function disabled(): static
    {
        $this->attributes[__FUNCTION__] = true;

        return $this;
    }

    public function getFieldClass(string|array|Arrayable $class = []): string
    {
        $class = empty($class) ? 'layui-form-item' : $class;

        $display = $this->form->inline || $this->inline ? 'layui-inline' : 'layui-block';

        return collect([$class, $display])->flatten()->join(' ');
    }

    protected function formatLabel(): string
    {
        if($this->label === '') {
            return '';
        }

        return <<<HTML
<label class="layui-form-label">{$this->label}</label>
HTML;
    }

    protected function formatAttributes(): string
    {
        return collect($this->attributes)
            ->map(function($value, $key) {
                switch ($key) {
                    case 'style': {
                        return $key . '=' . '"' . collect($value)->map(function($value, $key) { return $key . ':' . $value; })->implode(';') . '"';
                    };
                    case 'class': {
                        return $key . '=' . '"' . collect($value)->implode(' ') . '"';
                    };
                    case 'lay-verify': {
                        return 'lay-verify' . '=' . '"' . collect($value)->implode('|') . '"';
                    };
                    default: {
                        return $key . '=' . '"' . $value . '"';
                    }
                }
            })
            ->implode(' ');
    }

    public function render(): string
    {
        return '';
    }

    public function run(): string
    {
        return '';
    }
}
