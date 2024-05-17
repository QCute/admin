<?php

namespace App\Admin\Database;

use App\Admin\Services\Extend\ChannelService;
use App\Admin\Services\Extend\DatabaseService;
use App\Admin\Services\Extend\ServerService;
use Exception;
use Illuminate\Database\MySqlConnection;

class DistributionConnection extends MySqlConnection
{
    /**
     * The rules.
     *
     * @var array
     */
    private $rules = [];

    public function __construct(array $rules = [])
    {
        $this->rules = $rules;

        // dummp connection pdo
        $connection = DatabaseService::getConnection();
        $pdo = $connection->getPdo();
        $config = $connection->getConfig();

        parent::__construct($pdo, '', '', $config);
    }

    /**
     * Run a select statement against the database.
     *
     * @param  string  $query
     * @param  array  $bindings
     * @param  bool  $useReadPdo
     * @return array
     */
    public function select($query, $bindings = [], $useReadPdo = true)
    {
        $channels = [];
        $channel = ChannelService::getChannel();
        if(is_null($channel)) {
            return [];
        }

        if($channel->tag == 'ALL') {
            // all channels
            $channels = ChannelService::getChannels();
        } else {
            // this channel
            $channels = collect([$channel]);
        }

        $servers = [];
        $server = ServerService::getServer();
        if($server->tag == 'ALL') {
            // all/this channel all server
            $servers = $channels->map(function($channel) { return $channel->servers; })->flatten();
        } else {
            // all/this channel this server
            $servers = collect([$server]);
        }

        $acc = [];
        $sql = self::parse($query);

        if(isset($sql->offset)) {
            new Exception('Distribution offset does not supported');
        }
        
        foreach($servers as $server) {

            if($server->tag == 'ALL') continue;

            // change connection
            $this->setDatabaseName($server->db_name);
            $this->setPdo(DatabaseService::changePDO($server, $this->getConfig()));

            // fetch from database
            $data = parent::select($query, $bindings, $useReadPdo);

            // fold
            $acc = $this->groupBy($sql, $acc, $data);

            // sort
            $acc = $this->orderBy($sql, $acc);

            // distribute offset not supported
            // $acc = $this->offset($sql, $acc);

            // cut
            $acc = $this->limit($sql, $acc);

        }

        return array_values($acc);
    }

    public static function parse(string $query): object
    {
        // split part
        $quote = '';
        $acc = '';
        $list = [];
        $query = mb_str_split($query);
        foreach ($query as $index => $char) {
            switch($char) {
                case '`':
                case '\'':
                case '"': {

                    if(empty($quote)) {
                        if(!empty($acc)) {
                            $list[] = $acc;
                        }
                        $quote = $char;
                        $acc = $char;
                        break;
                    }

                    $acc .= $char;

                    // this quote
                    if($quote == $char) {

                        // not end
                        $pre = $query[$index - 1];
                        if($pre == '\\') {
                            break;
                        }

                        // end
                        if(!empty($acc)) {
                            $list[] = $acc;
                        }
                        $quote = '';
                        $acc = '';

                        break;
                    }

                };break;
                case ' ': {
                    if(!empty($acc)) {
                        $list[] = $acc;
                    }
                    $acc = '';
                    break;
                }
                case ',':
                case '(':
                case ')': {
                    if(empty($quote)) {
                        
                        if(!empty($acc)) {
                            $list[] = $acc;
                        }
                        $list[] = $char;
                        $acc = '';
                        break;
                    }
                    
                    $acc .= $char;
                };break;
                default: {
                    $acc .= $char;
                }
            }

        }

        if($acc !== '') {
            $list[] = $acc;
            $acc = '';
        }

        // parse part
        $quote = 0;
        $operate = '';
        $sql = [
            'row' => 0,
            'where' => [],
            'group' => [],
            'having' => [],
            'order' => [],
            'limit' => [],
            'offset' => [],
        ];

        foreach($list as $block) {
            switch (strtolower($block)) {
                case '(': {
                    if(!empty($operate)) {
                        $sql[$operate][] = $block;
                        break;
                    }
                    $quote++;
                };break;
                case ')': {
                    if(!empty($operate)) {
                        $sql[$operate][] = $block;
                        break;
                    }
                    $quote--;
                };break;
                case 'group':
                case 'order': {
                    if($quote > 0) {
                        break;
                    }

                    $operate = $block;
                };break;
                case 'by':break;
                case 'asc':
                case 'desc': {
                    if($quote > 0) {
                        break;
                    }

                    if(empty($operate)) {
                        break;
                    }

                    $sql[$operate][] = $block;
                };break;
                case 'having': {
                    $operate = '';
                }break;
                case 'limit':
                case 'offset': {
                    $operate = $block;
                };break;
                default: {
                    if($quote > 0) {
                        break;
                    }

                    if(empty($operate)) {
                        break;
                    }

                    if($operate == 'group') {
                        $sql[$operate][] = $block;
                        break;
                    }

                    if($operate == 'order') {
                        $sql[$operate][] = $block;
                        break;
                    }

                    if($operate == 'limit') {
                        $sql[$operate][] = $block;
                        break;
                    }

                    if($operate == 'offset') {
                        $sql[$operate][] = $block;
                        break;
                    }

                    $sql[$operate][] = trim($block, '`');
                }
            }
        }

        // group by part
        $quote = 0;
        $operate = '';
        $acc = '';
        $group = [];
        foreach($sql['group'] as $block) {
            switch (strtolower($block)) {
                case ',': {
                    if($quote > 0) {
                        break;
                    }

                    $group[] = trim($acc, '`');
                    $acc = '';
                };break;
                case '(': {
                    $acc .= $block;
                    $quote++;
                };break;
                case ')': {
                    $acc .= $block;
                    $quote--;
                };break;

                default: {
                    $acc .= $block;
                }
            }
        }

        if(!empty($acc)) {
            $group[] = trim($acc, '`');
        }

        $sql['group'] = $group;

        // order by part
        $quote = 0;
        $operate = '';
        $acc = '';
        $key = '';
        $order = [];
        foreach($sql['order'] as $block) {
            switch (strtolower($block)) {
                case ',': {
                    if($quote > 0) {
                        break;
                    }

                    $group[] = trim($acc, '`');
                    $acc = '';
                };break;
                case '(': {
                    $acc .= $block;
                    $quote++;
                };break;
                case ')': {
                    $acc .= $block;
                    $quote--;
                };break;
                case 'asc':
                case 'desc': {
                    if($quote > 0) {
                        break;
                    }

                    $order[trim($acc, '`')] = $block;
                };break;
                default: {
                    $acc .= $block;
                }
            }
        }

        if(!empty($acc)) {
            $order[trim($acc, '`')] = 'asc';
        }

        $sql['order'] = $order;

        // limit part
        $limit = array_filter($sql['limit'], function($value) { return !empty($value);});
        $sql['limit'] = !empty($limit[0]) ? intval($limit[0]): null;
        $sql['offset'] = !empty($limit[1]) ? intval($offset) : $sql['offset'];

        // offset part
        $offset = array_filter($sql['offset'], function($value) { return !empty($value);});
        $sql['offset'] = !empty($offset) ? intval($offset) : null;

        return (object)$sql;
    }

    /**
     * Run a select group by statement against the database.
     *
     * @param  object  $sql
     * @param  array  $acc
     * @param  array  $data
     * @return array
     */
    public function groupBy(object &$sql, array $acc, array $data): array
    {
        if(empty($sql->group)) {
            return array_merge($acc, $data);
        }

        // update data into acc
        foreach ($data as $item) {

            // union key
            $key = implode('-', array_map(function ($name) use ($item) {
                return $name . ':' . $item->{$name}; 
            }, $sql->group));
            $row = $acc[$key] ?? null;

            if (is_null($row)) {
                $acc[$key] = $item;
                continue;
            }
  
            // merge each property
            foreach ($item as $name => $value) {

                // skip key
                if (in_array($name, $sql->group)) {
                    continue;
                }

                // custom rules
                $rule = $this->rules[$name] ?? null;
                if (!is_null($rule)) {
                    $row->{$name} = call_user_func($rule, $row->{$name}, $value);
                    continue;
                }

                // plus by default
                $row->{$name} += $value;
            }

            // update
            $acc[$key] = $row;
        }

        return $acc;
    }

    /**
     * Run a select order by statement against the database.
     *
     * @param  object  $sql
     * @param  array  $acc
     * @return array
     */
    public function orderBy(object &$sql, array $acc): array
    {
        if(empty($sql->order)) {
            return $acc;
        }

        uasort($acc, function ($x, $y) use ($sql) {
            $flag = false;

            foreach ($sql->order as $name => $order) {

                if ($order == 'asc') {
                    $flag = $flag && $x->{$name} > $y->{$name};
                } else {
                    $flag = $flag && $x->{$name} < $y->{$name};
                }

            }

            return $flag;
        });

        return $acc;
    }

    /**
     * Run a select limit statement against the database.
     *
     * @param  object  $sql
     * @param  array  $acc
     * @return array
     */
    public function limit(object &$sql, array $acc): array
    {
        if($sql->limit < count($acc)) {
            return $acc;
        }

        $acc = array_slice($acc, 0, $sql->limit);

        // stop
        return $acc;
    }
}
