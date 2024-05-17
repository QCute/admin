<?php

namespace App\Admin\Controllers\Assistant;

use App\Admin\Controllers\Controller;

class FormBuilderTestController extends Controller
{
    public function index()
    {
        $form = new \App\Admin\Builders\Form();
        $form->name('wow');

        $form->text('ax')->id('ax')->class(['c'])->placeholder('d')->value('e')->required()->label('x');
        $form->hidden('aax')->id('bx')->class(['c'])->placeholder('d')->value('e')->required()->label('y');
        $form->password('acx')->id('cx')->class(['c'])->placeholder('d')->value('e')->required()->label('z');
        $form->display('adx')->id('dx')->class(['c'])->placeholder('d')->value('e')->required()->label('%');
        $form->number('ffadx')->id('ffdx')->class(['c'])->placeholder('d')->value('5')->required()->label('$')->min(6)->max(10);

        $form->year('a-year')->label('a-year')->value('2020');
        $form->month('a-month')->label('a-month')->value('2020-06');
        $form->date('a-date')->label('a-date')->value('2020-06-06');
        $form->time('a-time')->label('a-time')->value('11:22:33');
        $form->dateTime('a-date-time')->label('a-date-time')->value('2020-06-06 22:33:44');
        $form->dateRange('a-date-range')->label('a-date-range');
        $form->timeRange('a-time-range')->label('a-time-range');
        $form->dateTimeRange('a-date-time-range')->label('a-date-time-range');

        $form->switchBox('wowwow')->label('is open')->value('ON');

        $ratio = $form->radio('abx')->id('fx')->label('fxx')->required();
        $ratio->option()->value('a')->label('aaa');
        $ratio->option()->value('b')->label('bbb');
        $ratio->option()->value('c')->label('ccc');

        $check = $form->checkBox('afx')->id('cfx')->label('cfx')->required();
        $check->option()->value('a')->label('aaa');
        $check->option()->value('b')->label('bbb');
        $check->option()->value('c')->label('ccc');

        $form->text('xyz-text')->label('xyz-text');
        $form->text('xyz-text-link')->label('xyz-text')->help('help link', 'link', ['url' => 'https://www.baidu.com']);
        $form->text('xyz-text-popup')->label('xyz-text')->help('help msg', 'msg', ['message' => 'this is help message']);
        $form->text('xyz-text-layer')->label('xyz-text')->help('help popup', 'layer', ['title' => 'this is help title', 'content' => 'this is help content']);

        $form->textArea('xyz-area')->label('xyz-area');
        $form->textArea('xyz-area-link')->label('xyz-area')->help('help link', 'link', ['url' => 'https://www.baidu.com']);
        $form->textArea('xyz-area-popup')->label('xyz-area')->help('help msg', 'msg', ['message' => 'this is help message']);
        $form->textArea('xyz-area-layer')->label('xyz-area')->help('help popup', 'layer', ['title' => 'this is help title', 'content' => 'this is help content']);

        $select = $form->select('xxx')->label('ppp');
        $select->option()->value('yyy')->label('yyy');
        $select->option()->value('xxx')->label('xxx');
        $select->option()->value('zzz')->label('zzz');

        $form->file('file')->label('file')->value('https://lf6-cdn-tos.bytegoofy.com/obj/goofy/star/idou_fe/assets/logo-ec94bd7f.svg');
        
        $form->image('img')->label('img'); //->value('https://lf6-cdn-tos.bytegoofy.com/obj/goofy/star/idou_fe/assets/logo-ec94bd7f.svg');
        $form->image('image')->label('image')->value('https://lf6-cdn-tos.bytegoofy.com/obj/goofy/star/idou_fe/assets/logo-ec94bd7f.svg');

        $transfer = $form->transfer('transfer')->label('roles')->title('Left', 'Right');
        $transfer->left()->value('ls')->label('this is ls');
        $transfer->left()->value('lv')->label('this is lv');
        $transfer->left()->value('lt')->label('this is lt');
        $transfer->right()->value('rs')->label('this is rs');
        $transfer->right()->value('rv')->label('this is rv');

        $form->icon('icon')->label('m-icon')->value('layui-icon-face-smile');
        $form->icon('icon')->label('m-icon')->value('fa fa-dashboard');
        $form->iconPicker('icon-picker')->label('m-icon-picker')->value('layui-icon layui-icon-moon');
        $form->iconPicker('icon-picker-x')->label('m-icon-picker-x')->value('layui-icon-question');
        $form->html('transfer-html')->label('a-html')->value('<div><p>this is html</p></div>');

        $form->button('a-btn')->label('a-btn')->name('a-btn-value');

        // $select = $form->multipleSelect('m-select')->label('m-select');
        // $select->option()->value('a')->label('a');
        // $select->option()->value('b')->label('b')->select(true);
        // $select->option()->value('c')->label('c')->select(true);

        $tags = $form->tags('tags')->label(trans('admin.role'));
        $tags->tag()->value('a')->label('a');
        $tags->tag()->value('b')->label('b');
        $tags->tag()->value('c')->label('c');

        
        return $form->build();
    }
}
