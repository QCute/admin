<?php

namespace App\Admin\Builders;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\View\View;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Support\Collection;

class Chart
{
    /**
     * The date registered on the controller.
     *
     * @var Form
     */
    private $form;

    /**
     * The key registered on the controller.
     *
     * @var string
     */
    private $key = '';

    /**
     * The value registered on the controller.
     *
     * @var Collection
     */
    private $value = [];

    /**
     * The data registered on the controller.
     *
     * @var Collection
     */
    private $data;

    /**
     * The type registered on the controller.
     *
     * @var string
     */
    private $type;

    /**
     * The option registered on the controller.
     *
     * @var array
     */
    private $option;

    public function form(Form $form): static 
    {
        $this->form = $form;

        return $this;
    }

    public function key(string $key): static 
    {
        $this->key = $key;

        return $this;
    }

    public function value(array|Arrayable $value): static 
    {
        $this->value = collect($value);

        return $this;
    }

    public function line(array|Arrayable $data, array $options = []): static
    {
        $this->type = __FUNCTION__;

        return $this->chart($this->type, $data, $options);
    }

    public function bar(array|Arrayable $data, array $options = []): static
    {
        $this->type = __FUNCTION__;

        return $this->chart($this->type, $data, $options);
    }

    public function pie(array|Arrayable $data, array $options = []): static
    {
        $this->type = __FUNCTION__;

        $this->option['series'] = [
            'data' => $data,
            'type' => $this->type,
        ] + $options;

        return $this;
    }

    public function chart(string $type, array|Arrayable $data, array $options = []): static
    {
        $this->data = collect($data);

        $series = $this->value->map(function($comment, $name) use ($type, $options) {

            $data = $this->data->pluck($name);

            return [
                'name' => $comment,
                'data' => $data,
                'type' => $type,
                'smooth' => true,
            ] + $options;
        });

        $this->option['series'] = array_values($series->toArray());

        return $this;
    }

    public function xAxis(string $type = 'category', array|Arrayable|null $default = null, array $options = []): static
    {
        if($type == 'category') {
            $data = collect($this->data);
            $data = $data->isEmpty() ? $default->toArray() : $data->pluck($this->key);

        } else if($type == 'value') {
            $data = [];

            if(collect($this->data)->isEmpty()) {
                $this->{$this->type}($default);
            }
        }

        $this->option[__FUNCTION__] = [
            'type' => $type,
            'data' => $data,
        ] + $options;

        return $this;
    }

    public function yAxis(string $type = 'value', array|Arrayable|null $default = null, array $options = []): static
    {
        if($type == 'category') {
            $data = collect($this->data);
            $data = $data->isEmpty() ? $default->toArray() : $data->pluck($this->key);

        } else if($type == 'value') {
            $data = [];

            if(collect($this->data)->isEmpty()) {
                $this->{$this->type}($default);
            }
        }

        $this->option[__FUNCTION__] = [
            'type' => $type,
            'data' => $data,
        ] + $options;

        return $this;
    }

    public function grid(string|null $top = null, string|null $right = null, string|null $bottom = null, string|null $left = null, array|null $options = []): static
    {
        $this->option[__FUNCTION__] = [
            'top' => $top,
            'right' => $right,
            'bottom' => $bottom,
            'left' => $left,
        ] + $options;

        return $this;
    }

    public function legend(string|null $top = null, string|null $right = null, string|null $bottom = null, string|null $left = null, array|null $options = []): static
    {
        $this->option[__FUNCTION__] = [
            'show' => true,
            'top' => $top,
            'right' => $right,
            'bottom' => $bottom,
            'left' => $left,
        ] + $options;

        return $this;
    }

    public function color(array $color): static
    {
        $this->option[__FUNCTION__] = $color;

        return $this;
    }

    public function option(): array
    {
        return $this->option;
    }

    public function build(): View|ViewFactory
    {
        $data = [
            'form' => $this->form ?? NULL,
            'chart' => $this->option(),
        ];

        return admin_view('public/chart', $data);
    }
}
