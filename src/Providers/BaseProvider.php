<?php

namespace Legacy\ThePit\providers;

use pocketmine\utils\Config;

abstract class BaseProvider
{
    abstract public function get(mixed $k, mixed $default = null);
    abstract public function set(mixed $k, mixed $v);
    abstract public function getConfig(): Config;
    abstract public function getNested(mixed $k, mixed $default = null);
    abstract public function setNested(mixed $k, mixed $v);
    abstract public function getName(): string;
    abstract public function getPath() : string;
    abstract public function dump() : array;

    public function __toString(): string
    {
        return "Provider name : ". $this->getName() . " | Provider path : " . $this->getPath();
    }

}