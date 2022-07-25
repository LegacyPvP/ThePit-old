<?php

namespace Legacy\ThePit\Databases;

use Legacy\ThePit\Core;
use pocketmine\utils\Config;

final class BaseDatabase extends Database
{
    public function __construct(protected string $name, protected string $path)
    {
        parent::__construct($name, $path);
    }

    public function getConfig(): Config
    {
        $default = yaml_parse(file_get_contents(Core::getFilePath() . "resources/$this->name.yml")) ?: [];
        return new Config($this->path, Config::YAML, $default);
    }
}