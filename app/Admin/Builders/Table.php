<?php

namespace App\Admin\Builders;

use App\Admin\Builders\Table\Page;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\View\View;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Support\Collection;

class Table
{
    /**
     * The name registered on the controller.
     *
     * @var string
     */
    private $name = '';

    /**
     * The form registered on the controller.
     *
     * @var Form
     */
    private $form;

    /**
     * The chart registered on the controller.
     *
     * @var Chart
     */
    private $chart;

    /**
     * The left registered on the controller.
     *
     * @var Collection
     */
    private $left;

    /**
     * The right registered on the controller.
     *
     * @var Collection
     */
    private $right;

    /**
     * The operation registered on the controller.
     *
     * @var Collection
     */
    private $operation;

    /**
     * The header registered on the controller.
     *
     * @var Collection
     */
    private $header;

    /**
     * The data registered on the controller.
     *
     * @var Collection
     */
    private $data;

    /**
     * The group registered on the controller.
     *
     * @var Collection
     */
    private $group;

    /**
     * The component registered on the controller.
     *
     * @var Collection
     */
    private $component;

    /**
     * The template registered on the controller.
     *
     * @var Collection
     */
    private $template;

    /**
     * The script registered on the controller.
     *
     * @var Collection
     */
    private $script;

    /**
     * The paginate registered on the controller.
     *
     * @var Page
     */
    private $paginate;

    /**
     * The option registered on the controller.
     *
     * @var array
     */
    private $option = [];

    public function form(Form $form): static 
    {
        $this->form = $form;

        return $this;
    }

    public function chart(Chart $chart): static 
    {
        $this->chart = $chart;

        return $this;
    }

    public function left(array|Arrayable $tools): static 
    {
        $this->left = collect($tools);

        return $this;
    }

    public function right(array|Arrayable $tools): static 
    {
        $this->right = collect($tools);

        return $this;
    }

    public function operation(array|Arrayable $tools): static
    {
        $this->operation = collect($tools);

        return $this;
    }

    public function header(array|Arrayable $header): static
    {
        $this->header = collect($header);

        return $this;
    }

    public function data(array|Arrayable $data): static
    {
        $this->data = collect($data);

        return $this;
    }

    public function group(array|Arrayable $group): static
    {
        $this->group = collect($group);

        return $this;
    }

    public function component(array|Arrayable $components): static
    {
        $this->component = collect($components);

        return $this;
    }

    public function template(array|Arrayable $templates): static
    {
        $this->template = collect($templates);

        return $this;
    }

    public function script(array|Arrayable $scripts): static
    {
        $this->script = collect($scripts);

        return $this;
    }

    public function paginate(Page $paginate): static
    {
        $this->paginate = $paginate;

        return $this;
    }

    public function option(): array
    {
        $this->option = [
            'form' => $this->form ?? NULL,
            'chart' => $this->chart ?? NULL,
            'left' => $this->left ?? collect([]),
            'right' => $this->right ?? collect([]),
            'operation' => $this->operation ?? collect([]),
            'header' => $this->header ?? collect([]),
            'data' => $this->data ?? collect([]),
            'group' => $this->group ?? collect([]),
            'component' => $this->component ?? collect([]),
            'template' => $this->template ?? collect([]),
            'script' => $this->script ?? collect([]),
            'paginate' => $this->paginate ?? NULL,
        ];

        return $this->option;
    }

    public function build(): View|ViewFactory
    {
        return admin_view('public/table', $this->option());
    }
}
