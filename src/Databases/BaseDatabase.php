<?php

namespace Legacy\ThePit\databases;

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
        return new Config($this->path, Config::DETECT);
    }
}