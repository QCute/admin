<?php

namespace App\Admin\Builders\Form;
use App\Admin\Builders\Form;

class DateRange extends Field
{
    /** The begin registered on the controller.
     * 
     * @var string
     */
    public $begin;

    /** The end registered on the controller.
     * 
     * @var string
     */
    public $end;

    /**
     * The attributes registered on the controller.
     * 
     * @var array
     */
    public $attributes = [
        'class' => ['layui-input'],
    ];

    public function __construct(Form $form, string $name)
    {
        $this->begin = date('Y-m-d');
        $this->end = date('Y-m-d');

        parent::__construct($form, $name);
    }

    public function begin(string $begin): static
    {
        $this->begin = $begin;

        return $this;
    }

    public function end(string $end): static
    {
        $this->end = $end;

        return $this;
    }

    public function value(string $value): static
    {
        [$begin, , $end] = explode(' ', trim($value));

        $this->begin = $begin;
        $this->end = $end;

        return $this;
    }

    public function render(): string
    {
        $name = $this->attributes['name'];

        $label = $this->formatLabel();

        return <<<HTML
<div class="layui-form-item layui-{$this->getDisplay()}">
    $label
    <div name="wrap-{$name}" class="layui-inline">
        <input type="hidden" name="{$name}" value="{$this->begin} - {$this->end}">
        <div class="layui-input-inline layui-input-wrap">
            <input type="text" class="layui-input" value="{$this->begin}" />
        </div>
        <div class="layui-form-mid">-</div>
        <div class="layui-input-inline layui-input-wrap">
            <input type="text" class="layui-input" value="{$this->end}" />
        </div>
    </div>
</div>
HTML;
    }

    public function run(): string
    {
        $selector = '[name="' . $this->attributes['name'] . '"]';
        $wrapSelector = '[name="wrap-' . $this->attributes['name'] . '"]';
        $beginSelector = '[name="begin' . '-' . $this->attributes['name'] . '"]';
        $endSelector = '[name="end' . '-' . $this->attributes['name'] . '"]';

        return <<<JAVASCRIPT
            // render
            layui.laydate.render({
                elem: '{$wrapSelector}',
                range: ['{$beginSelector}', '{$endSelector}'],
                value: '{$this->begin} - {$this->end}',
                type: 'date',
                done: function (value, begin, end) {
                    const [beginDate, _, endDate] = value.trim().split(' ');
                    document.querySelector('{$selector}').value = beginDate + ' - ' + endDate;
                }
            });
JAVASCRIPT;
    }
}
