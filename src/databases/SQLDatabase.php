<?php

namespace Legacy\ThePit\databases;

use Legacy\ThePit\managers\Managers;
use SQLite3;
use SQLite3Result;

final class SQLDatabase extends Database
{
    public function __construct(string $name, string $path)
    {
        $this->name = $name;
        $this->path = $path;
        $db = $this->getConfig();
        $db->exec("CREATE TABLE ");
        parent::__construct($name, $path);
    }

    public function get(mixed $k, mixed $default = null, string $table = "users"): mixed
    {
        return ((($result = $this->getConfig()->query("SELECT $k FROM $table")) instanceof SQLite3Result) ? $result->fetchArray() : false) ?: $default;
    }

    public function getNested(mixed $k, mixed $default = null, string $table = "users"): mixed
    {
        return (($result = $this->getConfig()->query("SELECT $k FROM $table WHERE ")) instanceof SQLite3Result ? $result?->fetchArray() : false) ?: $default;
    }

    public function set(mixed $k, mixed $v): void
    {
        $this->getConfig()->query('INSERT () INTO users');
    }

    /**
     * @return SQLite3
     */
    public function getConfig(): SQLite3
    {
        return new SQLite3(Managers::DATA()->get('config')->getNested('database.path', 'root/legacy.db'));
    }
}