<?php

namespace Legacy\ThePit\Providers;

use pocketmine\utils\Config;

abstract class BaseProvider
{
    abstract public function get(mixed $k);
    abstract public function set(mixed $k, mixed $v);
    abstract public function getConfig(): Config;
    abstract public function getNested(mixed $k);
    abstract public function setNested(mixed $k, mixed $v);
    abstract public function getName(): string;
    abstract public function getPath() : string;

    public function __toString(): string
    {
        return "Provider name : ". $this->getName() . " | Provider path : " . $this->getPath();
    }

}