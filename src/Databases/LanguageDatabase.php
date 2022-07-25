<?php

namespace Legacy\ThePit\Databases;

use Legacy\ThePit\Core;
use pocketmine\utils\Config;

final class LanguageDatabase extends Database
{
    public function __construct(protected string $name)
    {
        parent::__construct($this->name, Core::getFilePath() . "resources/languages/" . "$this->name.yml");
    }

    public function getConfig(): Config
    {
        return new Config($this->path, Config::YAML);
    }
}