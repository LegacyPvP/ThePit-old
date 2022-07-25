<?php

namespace Legacy\ThePit\databases;

use Legacy\ThePit\Core;
use pocketmine\utils\Config;

final class LanguageDatabase extends Config
{
    public function __construct(string $file, int $type = Config::YAML, array $default = [])
    {
        $default = yaml_parse(file_get_contents(Core::getFilePath() . "resources/languages/" . "lang_$file.yml")) ?: [];
        parent::__construct(Core::getInstance()->getDataFolder() . "languages/lang_" . $file . ".yml", $type, $default);
    }
}