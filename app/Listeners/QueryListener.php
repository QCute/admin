<?php

namespace App\Listeners;

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\Log;

class QueryListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \Illuminate\Database\Events\QueryExecuted  $event
     * @return void
     */
    public function handle(QueryExecuted $event)
    {
        if (!env('LOG_SQL', false)) return;
        if(empty($event->bindings)) return;
        foreach ($event->bindings as $i => $binding) {
            if ($binding instanceof DateTime) {
                $event->bindings[$i] = $binding->format("'Y-m-d H:i:s'");
            } else {
                if (is_string($binding)) {
                    $event->bindings[$i] = "'$binding'";
                }
            }
        }
        $log = "";
        for($i = 0; $i < strlen($event->sql); $i++) {
            if($event->sql[$i] == '?') {
                $log .= array_shift($event->bindings);
            } else {
                $log .= $event->sql[$i];
            }
        }
        Log::channel('sql')->info($log);
    }
}
