<?php

namespace App\Admin\Builders\Form;

class Text extends Field
{
    /**
     * The prefix registered on the controller.
     * 
     * @var string
     */
    public $prefix = '';

    /**
     * The suffix registered on the controller.
     * 
     * @var string
     */
    public $suffix = '';

    /**
     * The help registered on the controller.
     * 
     * @var string
     */
    public $help = '';

    /**
     * The attributes registered on the controller.
     * 
     * @var array
     */
    public $attributes = [
        'class' => ['layui-input'],
        'type' => 'text',
    ];

    public function prefix(string $prefix): static
    {
        $this->prefix = $prefix;

        return $this;
    }

    public function suffix(string $suffix): static
    {
        $this->attributes['lay-affix'] = $suffix;

        return $this;
    }

    public function value(string $value): static
    {
        $this->attributes[__FUNCTION__] = $value;

        return $this;
    }

    public function placeholder(string $placeholder): static
    {
        $this->attributes[__FUNCTION__] = $placeholder;

        return $this;
    }

    public function help(string $text = '', string $type = 'link', array $options = [], array $iconStyle = [], array $textStyle = []): static
    {
        // type:
        // 1. link => target=_blank
        //     1. url: string
        //     2. target: string
        // 2. popup => popup message
        //     1. message: string
        // 3. layer => window
        //     1. title: string
        //     2. content: string

        $textStyle = $textStyle + [
            'margin-left' => '4px',
            'font-size' => '16px',
            'font-weight' => 'bold',
            'color' => 'var(--global-primary-color)',
        ];
        $textStyle = collect($textStyle)
            ->map(function ($value, $key) {
                return $key . ':' . $value;
            })
            ->implode(';');

        switch($type) {
            case 'link': {
                $url = $options['url'];
                $target = $options['target'] ?? '_blank';
                $text = <<<HTML
<a href='{$url}' target='{$target}' style='{$textStyle}'>{$text}</a>
HTML;
            };break;
            case 'msg': {
                $message = $options['message'];
                $text = <<<HTML
<a href='javascript:;' onclick='layer.msg("{$message}")' style='{$textStyle}'>{$text}</a>
HTML;
            };break;
            case 'layer': {
                $width = $options['width'] ?? '960px';
                $height = $options['height'] ?? '540px';
                $title = $options['title'];
                $content = $options['content'];
                $text = <<<HTML
<script>
    function openLayer() {
        layer.open({
            type: 1,
            area: ['{$width}', '{$height}'],
            title: `{$title}`,
            content: `{$content}`,
            shade: 0.6,
            shadeClose: false,
            anim: 0,
        });
    }
</script>
<a href='javascript:;' onclick='openLayer()' style='{$textStyle}'>{$text}</a>
HTML;
            };break;
            default: throw new \Exception("Unknown help type: $type");
        }

        $iconStyle = $iconStyle + [
            'font-weight' => 'bold',
            'color' => 'var(--global-primary-color)',
        ];
        $iconStyle = collect($iconStyle)
            ->map(function ($value, $key) {
                return $key . ':' . $value;
            })
            ->implode(';');

        $this->help = <<<HTML
<label class="layui-form-label" ></label>
<div style="padding: 4px; display: flex; align-items: center;">
    <i class="layui-icon layui-icon-tips" style='{$iconStyle}'></i>
    {$text}
</div>
HTML;

        return $this;
    }

    public function render(): string
    {
        $fieldClass = $this->getFieldClass();

        $label = $this->formatLabel();

        $attributes = $this->formatAttributes();

        $help = $this->help;

        if($this->prefix === '') {
            return <<<HTML
<div class="{$fieldClass}">
    $label
    <div class="layui-input-block">
        <input $attributes />
    </div>
    $help
</div>
HTML;
        }



        return <<<HTML
<div class="{$fieldClass}">
    $label
    <div class="layui-input-block layui-input-wrap">
        <div class="layui-input-prefix">
            <i class="{$this->prefix}"></i>
        </div>
        <div class="layui-input-block">
            <input $attributes />
        </div>
    </div>
    $help
</div>
HTML;
    }
}
