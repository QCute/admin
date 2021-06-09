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

use Encore\Admin\Form;
use Encore\Admin\Facades\Admin;
use App\Admin\Controllers\SwitchServerController;

// remove plugin
Form::forget(['map', 'editor']);
// vue
// Admin::js('https://cdn.jsdelivr.net/npm/vue@3.0.10/dist/vue.global.js');
// element+
// Admin::css('https://unpkg.com/element-plus/lib/theme-chalk/index.css');
// Admin::js('https://unpkg.com/element-plus/lib/index.full.js');
// axios
// Admin::js('https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js');
// echarts libs
Admin::js('https://cdn.bootcdn.net/ajax/libs/echarts/5.0.1/echarts.min.js');
// date picker
Admin::js('https://cdn.bootcdn.net/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.js');
// navigate bar
Admin::navbar(function (Encore\Admin\Widgets\Navbar $navbar) {
    $navbar->right(SwitchServerController::serverList());
});
