<?php

namespace Legacy\ThePit\databases;

use pocketmine\utils\Config;

interface IDatabase
{
    public function get(mixed $k, mixed $default = null);

    public function set(mixed $k, mixed $v);

    public function getConfig(): Config;

    public function getNested(mixed $k, mixed $default = null);

    public function setNested(mixed $k, mixed $v);

    public function getName(): string;

    public function getPath(): string;

    public function dump(): array;

}