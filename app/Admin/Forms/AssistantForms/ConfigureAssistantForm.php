<?php

namespace App\Admin\Forms\AssistantForms;

use App\Admin\Controllers\SwitchServerController;
use Encore\Admin\Traits\DefaultDatetimeFormat;
use Encore\Admin\Widgets\Form;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

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
     * @return  RedirectResponse
     */
    public function handle(Request $request): RedirectResponse
    {
        $data = $request->input("items", []);
        $items = array_map(function ($row) { return "{{$row['item_id']}, {$row['number']}}"; }, $data);
        $items = "[" . implode(", ", $items) . "]";
        // pass to session
        Session::put("generated-configure", $items);
        return back();
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
            ->setElementName('configure')
            ->help(trans("admin.click") . trans("admin.copy"))
            ->readonly();
        $this->table('items', trans("admin.items"), function ($table) {
            $data = SwitchServerController::getDB()
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
        // data
        $items = Session::remove("generated-configure");
        $this->html("
        <script>
            $(document).ready(function() {
                document.querySelector('[name=configure]').value = '$items';
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