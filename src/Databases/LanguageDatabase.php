<?php

namespace Legacy\ThePit\Databases;

use Legacy\ThePit\Core;
use pocketmine\utils\Config;

final class LanguageDatabase extends Config
{
    public function __construct(string $file, int $type = Config::YAML, array $default = [])
    {
        parent::__construct(Core::getInstance()->getDataFolder()."languages/lang_".$file.".yml", $type, $default);
    }
}