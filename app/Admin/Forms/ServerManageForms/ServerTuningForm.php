<?php

namespace App\Admin\Forms\ServerManageForms;

use App\Admin\Controllers\SwitchServerController;
use Encore\Admin\Traits\DefaultDatetimeFormat;
use Encore\Admin\Widgets\Form;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServerTuningForm extends Form
{
    use DefaultDatetimeFormat;

    /**
     * The form title.
     *
     * @var  string
     */
    public $title = '';

    /**
     * The server state.
     *
     * @var  bool
     */
    public bool $state = false;

    /**
     * The server owner.
     *
     * @var string
     */
    public string $owner = '';

    /**
     * Form constructor.
     *
     * @param bool $state
     * @param string $owner
     */
    public function __construct(bool $state, string $owner)
    {
        $this->state = $state;
        $this->owner = $owner;
        parent::__construct();
    }

    /**
     * Handle the form request.
     *
     * @param Request $request
     * @return  RedirectResponse
     */
    public function handle(Request $request): RedirectResponse
    {
        return back();
    }

    /**
     * Build a form here.
     *
     * @throws Exception
     */
    public function form()
    {
        $this->title = trans("admin.tuning");
        $this->disableReset();
        $this->disableSubmit();
        $server = SwitchServerController::getCurrentServer();
        // not supported
        if (empty(SwitchServerController::getServer($server)->ssh_host)) {
            $this
                ->display("")
                ->default("This server does not support tuning");
            return;
        }
        // lock check
        if (!empty($this->owner) && $this->owner != Auth::user()->name) {
            $this
                ->display("")
                ->default("This server lock by {$this->owner}");
            return;
        }
        // supported
        $path = request()->path();
        $this
            ->display("get-server-time", trans("admin.server_time"))
            ->with(function () {
                return "<a name='get-server-time'></a>";
            });

        $this
            ->display("get-local-time", trans("admin.local_time"))
            ->with(function () {
                return "<a name='get-local-time'></a>";
            });

        $datetime = SwitchServerController::execute(["date", "+%s"]);
        $this
            ->datetime("set-server-time", trans("admin.setting") . " " . trans("admin.server_date"))
            ->attribute("style","resize: vertical")
            ->default(date("Y-m-d H:i:s", intval($datetime)));
        $this->html("<a href='$path?action=set-server-time&time=' onclick='this.href += document.querySelector(\"[name=set-server-time]\").value'><button class='form-control btn btn-info' type='button'>" . trans("admin.setting") . "</button></a>");

        $openTime = SwitchServerController::executeMakerScript(["cfg", "get", $server, "main,open_time"]);
        $this
            ->display("get-open-time", trans("admin.open_time"))
            ->default(date("Y-m-d", intval($openTime)));
        $this
            ->date("set-server-open-time", trans("admin.setting") . " " . trans("admin.open_time"))
            ->attribute("style","resize: vertical")
            ->default(date("Y-m-d", intval($openTime)));
        $this->html("<a href='$path?action=set-server-open-time&time=' onclick='this.href += document.querySelector(\"[name=set-server-open-time]\").value'><button class='form-control btn btn-info' type='button'>" . trans("admin.setting") . "</button></a>");

        $this
            ->display("get-server-state", trans("admin.server_state"))
            ->with(function () {
                return "<a name='get-server-state'></a>";
            });

        // state
        if($this->state) {
            $this->html("<a href='$path?action=server-stop'><button class='form-control btn btn-warning' type='button'>" . trans("admin.server_stop") . "</button></a>");
        } else {
            $this->html("<a href='$path?action=server-start'><button class='form-control btn btn-success' type='button'>" . trans("admin.server_start") . "</button></a>");
            $this->html("<a href='$path?action=server-truncate'><button class='form-control btn btn-danger' type='button'>" . trans("admin.server_truncate") . "</button></a>");
        }
        // owner
        if (empty($this->owner)) {
            $this->html("<a href='$path?action=server-lock'><button class='form-control btn btn-primary' type='button'>" . trans("admin.server_lock") . "</button></a>");
        } else {
            $this->html("<a href='$path?action=server-lock'><button class='form-control btn btn-info' type='button'>" .  trans("admin.server_unlock") . "</button></a>");
        }
        // script
        $this->html("
            <script>
            // local time loop
            function getLocalTime() {
                const now = new Date();
                const year = now.getFullYear();
                const month = (now.getMonth() + 1).toString(10, 2).padStart(2, '0');
                const date = now.getDate().toString(10, 2).padStart(2, '0');
                const hour = now.getHours().toString(10, 2).padStart(2, '0');
                const minute = now.getMinutes().toString(10, 2).padStart(2, '0');
                const second = now.getSeconds().toString(10, 2).padStart(2, '0');
                const time = document.querySelector('[name=get-local-time]');
                if(time) {
                    time.text = year + '-' + month + '-' + date + ' ' + hour + ':' + minute + ':' + second;
                    setTimeout(() => getLocalTime(), 1000);
                }
            }
            // server time loop
            function getServerTime() {
                axios({ 'url': '$path' + '-get-server-time' }).then(({data}) => { 
                    const time = document.querySelector('[name=get-server-time]');
                    if(time) {
                        time.text = data;
                        setTimeout(() => getServerTime(), 1000);
                    } 
                });
            }
            // server state loop
            function getServerState() {
                axios({ 'url': '$path' + '-get-server-state' }).then(({data}) => {
                    const state = document.querySelector('[name=get-server-state]');
                    if(state) {
                        state.text = data;
                        setTimeout(() => getServerState(), 1000);
                    }
                })
            }
            $(function() {
                // time and state loop
                getLocalTime();
                getServerTime();
                getServerState();
            });
        </script>");

        $this->disableReset();
        $this->disableSubmit();
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