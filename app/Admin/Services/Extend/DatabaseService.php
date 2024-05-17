<?php

namespace App\Admin\Services\Extend;

use App\Admin\Models\Extend\ServerModel;
use \Illuminate\Database\Connection;
use Illuminate\Support\Facades\DB;
use PDO;

class DatabaseService
{
    /**
     * Get remote data connection
     *
     * @return string
     */
    public static function getName(): string
    {
        return 'remote';
    }

    /**
     * Get connection DB interface
     *
     * @return Connection
     */
    public static function getConnection(): Connection
    {
        return DB::connection(self::getName());
    }

    /**
     * Change remote data connection
     *
     * @param ServerModel $server
     * @return Connection
     */
    public static function changeConnection(ServerModel $server): Connection
    {
        // todo optimize same connection
        $name = self::getName();
        $config = config("database.connections.$name");

        // replace with pdo
        $connection = DB::connection($name);
        $connection->setDatabaseName($server->db_name);
        $connection->setPdo(self::changePDO($server, $config));
        return $connection;
    }

    /**
     * Get remote pdo.
     *
     * @param ServerModel $server
     * @return PDO
     */
    public static function changePDO(ServerModel $server, array $config): PDO
    {
        $driver = $config["driver"];
        $host = "host=$server->db_host";
        $port = "port=$server->db_port";
        $db = "dbname=$server->db_name";
        $charset = "charset={$config["charset"]}";
        $pdo = new PDO("$driver:$host;$port;$db;$charset", $server->db_username, $server->db_password, [PDO::ATTR_PERSISTENT => true]);
        return $pdo;
    }
}
