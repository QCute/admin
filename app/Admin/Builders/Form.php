<?php

namespace App\Admin\Builders;

use App\Admin\Builders\Form\Field;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\View\View;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Class Form.
 * @method Form\Button         button($name)
 * @method Form\CheckBox       checkBox($name)
 * @method Form\Date           date($name)
 * @method Form\DateRange      dateRange($name)
 * @method Form\DateTime       dateTime($name)
 * @method Form\DateTimeRange  dateTimeRange($name)
 * @method Form\Display        display($name)
 * @method Form\File           file($name)
 * @method Form\Hidden         hidden($name)
 * @method Form\Html           html($name)
 * @method Form\Icon           icon($name)
 * @method Form\IconPicker     iconPicker($name)
 * @method Form\Image          image($name)
 * @method Form\Logo           logo($name)
 * @method Form\Month          month($name)
 * @method Form\MultipleSelect multipleSelect($name)
 * @method Form\Number         number($name)
 * @method Form\Password       password($name)
 * @method Form\Radio          radio($name)
 * @method Form\Select         select($name)
 * @method Form\SwitchBox      switchBox($name)
 * @method Form\Tags           tags($name)
 * @method Form\Text           text($name)
 * @method Form\TextArea       textArea($name)
 * @method Form\Time           time($name)
 * @method Form\TimeRange      timeRange($name)
 * @method Form\Transfer       transfer($name)
 * @method Form\Year           year($name)

 

 * @method Form\CheckboxButton checkboxButton($name, $label = '')
 * @method Form\CheckboxCard   checkboxCard($name, $label = '')
 * @method Form\RadioButton    radioButton($name, $label = '')
 * @method Form\RadioCard      radioCard($name, $label = '')


 * @method Form\Id             id($name, $label = '')
 * @method Form\Ip             ip($name, $label = '')
 * @method Form\Url            url($name, $label = '')
 * @method Form\Color          color($name, $label = '')
 * @method Form\Email          email($name, $label = '')
 * @method Form\Mobile         mobile($name, $label = '')
 * @method Form\Slider         slider($name, $label = '')


 * @method Form\Currency       currency($name, $label = '')
 * @method Form\Rate           rate($name, $label = '')
 * @method Form\Divider        divider($title = '')
 * @method Form\Decimal        decimal($column, $label = '')
 * @method Form\Tags           tags($column, $label = '')

 * @method Form\Captcha        captcha($column, $label = '')
 * @method Form\Listbox        listbox($column, $label = '')
 * @method Form\Table          table($column, $label, $builder)
 * @method Form\Timezone       timezone($column, $label = '')
 * @method Form\KeyValue       keyValue($column, $label = '')
 * @method Form\ListField      list($column, $label = '')
 * @method mixed               handle(Request $request)
 */

class Form
{
    /**
     * The name registered on the controller.
     *
     * @var string
     */
    public $name = '';

    /**
     * The field registered on the controller.
     *
     * @var Collection
     */
    public $field;

    /**
     * The component registered on the controller.
     *
     * @var Collection
     */
    public $component;

    /**
     * The script registered on the controller.
     *
     * @var Collection
     */
    public $script;

    /**
     * The method registered on the controller.
     *
     * @var string
     */
    public $method = 'POST';

    /**
     * The action registered on the controller.
     *
     * @var string
     */
    public $action;

    /**
     * The align registered on the controller.
     *
     * @var string
     */
    public $align = 'left';

    /**
     * The inline registered on the controller.
     *
     * @var bool
     */
    public $inline = false;

    /**
     * The hide registered on the controller.
     *
     * @var bool
     */
    public $hide = false;

    /**
     * The disabled registered on the controller.
     *
     * @var bool
     */
    public $disabled = false;

    /**
     * The group registered on the controller.
     *
     * @var array
     */
    public $group = [];

    /**
     * The option registered on the controller.
     *
     * @var array
     */
    public $option = [];

    public function name(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function field(array|Arrayable $fields): static 
    {
        $this->field = collect($fields);

        return $this;
    }

    public function component(array|Arrayable $components): static
    {
        $this->component = collect($components);

        return $this;
    }

    public function script(array|Arrayable $scripts): static
    {
        $this->script = collect($scripts);

        return $this;
    }

    public function method(string $method = 'POST'): static
    {
        $this->method = $method;

        return $this;
    }

    public function action(string $action): static
    {
        $this->action = $action;

        return $this;
    }

    public function inline(bool $inline = true): static
    {
        $this->inline = $inline;

        return $this;
    }

    public function hide(bool $hide = true): static
    {
        $this->hide = $hide;

        return $this;
    }

    public function disabled(bool $disabled = true): static
    {
        $this->disabled = $disabled;

        return $this;
    }

    public function align(string $align = 'center'): static
    {
        $this->align = $align;

        return $this;
    }

    public function group(array $group = []): static
    {
        $this->group = $group;

        return $this;
    }

    public function option(): array
    {
        $this->option = [
            'name' => $this->name,
            'field' => $this->field ?? collect([]),
            'component' => $this->component ?? collect([]),
            'script' => $this->script ?? collect([]),
            'method' => $this->method,
            'hide' => $this->hide,
            'disabled' => $this->disabled,
            'group' => $this->group,
        ];

        return $this->option;
    }

    public function build(): View|ViewFactory
    {
        return admin_view('public/form', $this->option());
    }

    /**
     * Generate a Field object and add to form builder if Field exists.
     *
     * @param string $method
     * @param array  $arguments
     *
     * @return Field
     */
    public function __call($method, $arguments)
    {
        $class = __CLASS__ . '\\' . Str::studly($method);

        $field = new $class($this, Arr::get($arguments, 0), ...array_slice($arguments, 1));

        return tap($field, function ($field) {
            $this->field[] = $field;
        });
    }
}
