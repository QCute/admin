<?php

/**
 * Laravel-admin - admin builder based on Laravel.
 * @author z-song <https://github.com/z-song>
 *
 * Bootstraper for Admin.
 *
 * Here you can remove builtin form field:
 * Encore\Admin\Form::forget(['map', 'editor']);
 *
 * Or extend custom form field:
 * Encore\Admin\Form::extend('php', PHPEditor::class);
 *
 * Or require js and css assets:
 * Admin::css('/packages/prettydocs/css/styles.css');
 * Admin::js('/packages/prettydocs/js/main.js');
 *
 */

Encore\Admin\Form::forget(['map', 'editor']);
// jquery cookie
Encore\Admin\Facades\Admin::js('https://cdn.bootcdn.net/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js');
// echarts lib
Encore\Admin\Facades\Admin::js('https://cdn.bootcdn.net/ajax/libs/echarts/5.1.1/echarts.min.js');
// navigate bar
Encore\Admin\Facades\Admin::navbar(function (Encore\Admin\Widgets\Navbar $navbar) {
    $navbar->right(App\Admin\Controllers\SwitchServerController::list());
});
