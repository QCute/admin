<?php

namespace App\Admin\Forms\AssistantForms;

use App\Admin\Controllers\SwitchServerController;

use Symfony\Component\HttpFoundation\Cookie;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Encore\Admin\Widgets\Form;
use Encore\Admin\Traits\DefaultDatetimeFormat;
use Illuminate\Support\Arr;

class ConfigureAssistantForm extends Form
{
    use DefaultDatetimeFormat;

    /**
     * The form title.
     *
     * @var  string
     */
    public $title = '';

    /**
     * Handle the form request.
     *
     * @param Request $request
     *
     * @return  RedirectResponse
     */
    public function handle(Request $request): RedirectResponse
    {
        $data = $request->input("items", []);
        $items = array_map(function ($row) { return "{{$row['item_id']}, {$row['number']}}"; }, $data);
        $items = "[" . implode(", ", $items) . "]";
        $cookies = [
            Cookie::create("generated-configure", $items)->withHttpOnly(false)
        ];
        return back()->withCookies($cookies);
    }

    /**
     * Build a form here.
     */
    public function form()
    {
        $this->title = trans("admin.generate");
        $this
            ->textarea("content", trans("admin.content"))
            ->attribute("style","resize: vertical; cursor: pointer")
            ->help(trans("admin.click") . trans("admin.copy"))
            ->readonly();
        $this->table('items', trans("admin.items"), function ($table) {
            $data = DB::connection(SwitchServerController::getConnection())
                ->table("item_data")
                ->get(["item_id", "name"])
                ->toArray();
            $options = [];
            foreach ($data as $row) {
                $options[$row->item_id] = $row->name;
            }
            $table
                ->select("item_id", trans("admin.id"))
                ->options($options)
                ->required();
            $table
                ->number("number", trans("admin.number"))
                ->default(1)
                ->min(1)
                ->required();
        });
        $this->html("
        <script>
            $(document).ready(function() {
                $('textarea').click(function() {
                    this.select();
                    document.execCommand('Copy');
                });
                $('textarea').val($.cookie('generated-configure'));
                $.removeCookie('generated-configure');
            });
        </script>
        ");
    }

    /**
     * The data of the form.
     *
     * @return  array $data
     */
    public function data(): array
    {
        return [];
    }
}