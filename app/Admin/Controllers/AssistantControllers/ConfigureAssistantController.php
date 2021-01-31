<?php

namespace App\Admin\Controllers\AssistantControllers;

use Illuminate\Support\Facades\DB;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use App\Admin\Controllers\SwitchServerController;

class ConfigureAssistantController extends Controller
{
    public function index(Content $content)
    {
        $database = SwitchServerController::getCurrentServer();
        $list = DB::select("SELECT `item_id`, `name` FROM `{$database}`.`item_data`");
        $list = implode("", array_map(function ($row) { return "<option value='{$row->item_id}'>{$row->name}</option>"; }, $list));
        return $content->title('')->body("
            <div class='box box-info'>
                <div class='box-header with-border'>" . trans("admin.generate") . "</div>
                <form id='form' class='form-horizontal' action=''>
                    <div class='box-body'>    
                        <div class='form-group' style='display: none;'>
                            <label for='name' class='col-sm-2 control-label'></label>
                            <div class='col-sm-3'>
                                <div class='input-group'>
                                    <select class='form-control' name='id' value=''>
                                        <option value=''>无</option>
                                        {$list}
                                    </select>
                                </div>
                            </div>
                            <div class='col-sm-3'>
                                <div class='input-group'>
                                    <!-- <span class='input-group-addon' style='cursor: pointer;padding: 6px 6px;' onclick='this.parentNode.childNodes[3].value = Math.max(0, parseInt(this.parentNode.childNodes[3].value) - 1);' title='删除'><i class='fa fa-minus fa-fw'></i></span> -->
                                    <input type='number' name='number' value='1' min='1' style='text-align: center;' class='form-control' />
                                    <!-- <span class='input-group-addon' style='cursor: pointer;padding: 6px 6px;' onclick='this.parentNode.childNodes[3].value = parseInt(this.parentNode.childNodes[3].value) + 1;' title='删除'><i class='fa fa-plus fa-fw'></i></span> -->
                                </div>
                            </div>
                            <div class='col-sm-2'>
                                <div class='input-group' style='cursor: pointer;' onclick='this.parentNode.parentNode.parentNode.removeChild(this.parentNode.parentNode);'>
                                    <span class='input-group-addon'><i class='fa fa-times fa-fw'></i></span>
                                    <input type='button' class='form-control' value='" . trans("admin.delete") . "' />
                                    <!-- <input type='text' class='form-control' style='width:0;margin: 0;padding: 0;border-right: 0;' /> -->
                                </div>
                            </div>
                        </div>
                        <div class='form-group'>
                            <label for='name' class='col-sm-2 control-label'></label>
                            <div class='col-sm-8'>
                                <div class='input-group'>
                                    <span class='input-group-addon'><i class='fa fa-copy fa-fw'></i></span>
                                    <textarea id='item-list' class='form-control' rows='10' style='min-width:100%;cursor:pointer;' readonly></textarea>
                                </div>
                            </div>
                        </div>
                        <div class='form-group'>
                            <label for='name' class='col-sm-2 control-label'></label>
                            <div class='col-sm-8'>
                                <div class='input-group'>
                                    <span class='input-group-addon'><i class='fa fa-magic fa-fw'></i></span>
                                    <input type='button' class='form-control' value='" . trans("admin.generate"). "' onclick='generate()' />
                                </div>
                            </div>
                        </div>
                        <div class='form-group'>
                            <label for='name' class='col-sm-2 control-label'></label>
                            <div class='col-sm-3'>
                                <div class='input-group'>
                                    <div style='padding: 6px 12px;'>" . trans("admin.items") . "</div>
                                </div>
                            </div>
                            <div class='col-sm-3'>
                                <div class='input-group'>
                                    <div style='padding: 6px 12px;'>" . trans("admin.number") . "</div>
                                </div>
                            </div>
                            <div class='col-sm-2'>
                                <div class='input-group' style='cursor: pointer;' onclick='add()'>
                                    <div type='button' class='btn btn-default'><i class='fa fa-plus fa-fw'></i>" . trans("admin.add") . "</div>
                                </div>
                            </div>
                        </div>
                        
                        
                    </div>
                </form>
            </div></div>
    <script>
        function add() {
            let node = $('#form')[0].firstElementChild.firstElementChild.cloneNode(true);
            node.style.display = '';
            $('#form')[0].firstElementChild.append(node);
            $('select:last').select2();
        }
        function generate() {
            let string = [];
            let array = $('#form').serialize().split('&').map((i) => i.split('=')[1]);
            for (let index = 2; index < array.length; index +=2) {
                if (array[index] == 0) {
                    alert('第' + index / 2 + '个物品为空!');
                    return;
                } else if (array[index + 1] == 0) {
                    alert('第' + index / 2 + '个数量为零!');
                    return;
                }
                string.push('{' + array[index] + ', ' + parseInt(array[index + 1]) + '}');
            }
            $('#item-list').val('[\\n' + string.join(',\\n') + '\\n]');
            $('textarea')[0].select();
            document.execCommand('Copy');
        }
    </script>");
    }
}
