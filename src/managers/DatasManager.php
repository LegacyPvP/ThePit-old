<?php

namespace Legacy\ThePit\managers;

use JsonException;
use Legacy\ThePit\Core;
use Legacy\ThePit\databases\BaseDatabase;
use Legacy\ThePit\databases\Database;
use Legacy\ThePit\databases\IDatabase;
use Legacy\ThePit\databases\SQLDatabase;

final class DatasManager extends Managers
{
    /**
     * @param Database[] $databases
     */
    private array $databases = [];

    public function load(): void
    {
        $this->add(
            new BaseDatabase("config", Core::getFilePath() . "resources/config.yml"),
            new SQLDatabase("sql", Core::getInstance()->getDataFolder() . "data.sql")
        );
    }

    public function init(): void
    {
        $this->databases = Core::$cache["data"];
        unset(Core::$cache["data"]);
        foreach ($this->getAll() as $database) {
            Core::getInstance()->getLogger()->notice("[DATA] Provider: " . $database->getName() . " Loaded");
        }
    }

    public function add(Database ...$databases)
    {
        foreach ($databases as $database) {
            Core::$cache["data"][$database->getName()] = $database;
        }
    }

    /**
     * @param string $name
     * @return IDatabase|null
     */
    public function get(string $name): ?IDatabase
    {
        return $this->databases[$name] ?? reset($this->databases) ?: null;
    }

    /**
     * @return Database[]
     */
    public function getAll(): array
    {
        return $this->databases;
    }

    /**
     * @throws JsonException
     */
    public function saveAll(): void
    {
        foreach ($this->getAll() as $database) {
            $data = $database->dump();
            $config = $database->getConfig();
            $config->setAll($data);
            $config->save();
        }
    }
}