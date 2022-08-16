<?php

namespace Legacy\ThePit\databases;

use Legacy\ThePit\Core;
use Legacy\ThePit\librairies\libasynql\AwaitGenerator\Await;
use Legacy\ThePit\librairies\libasynql\DataConnector;
use Legacy\ThePit\librairies\libasynql\libasynql;
use Legacy\ThePit\player\LegacyPlayer;
use pocketmine\player\Player;

final class SQLDatabase extends Database
{

    public static DataConnector $dataBase;

    public function __construct(string $name, string $path)
    {
        $this->name = $name;
        $this->path = $path;

        self::$dataBase = libasynql::create(Core::getInstance(), Core::getInstance()->getConfig()->get("database"),
       ["sqlite" => "queries.psf"]);
        Await::g2c(self::$dataBase->asyncGeneric("table.create", ["name" => "Main"]),
            fn () => Core::getInstance()->getLogger()->notice("[SQL] Main table created"));

        parent::__construct($name, $path);
    }

    public function _GET(string $identifier)
    {
        return self::$dataBase->asyncSelect($identifier);
    }

    public function get(mixed $k, mixed $default = null, string $table = "users"): mixed
    {
       // ((($result = $this->getConfig()->query("SELECT $k FROM $table")) instanceof SQLite3Result) ? $result->fetchArray() : false) ?: $default
        return $this->_GET((string)$k);
    }

    /*public function getNested(mixed $k, mixed $default = null, string $table = "users"): mixed
    {
        return (($result = $this->getConfig()->query("SELECT $k FROM $table WHERE ")) instanceof SQLite3Result ? $result?->fetchArray() : false) ?: $default;
    }*/

    public function set(mixed $k, mixed $v): void
    {
        self::$dataBase->asyncInsert($k);
    }


    /**
     * @return DataConnector
     */
    public function getConfig(): DataConnector
    {
        return self::$dataBase;
    }

    /**
     * @return DataConnector
     */
    public static function getDatabase(): DataConnector
    {
        return self::$dataBase;
    }

}