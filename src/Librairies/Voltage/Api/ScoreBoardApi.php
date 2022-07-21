<?php

namespace Legacy\ThePit\Librairies\Voltage\Api;

use pocketmine\plugin\PluginBase;
use Legacy\ThePit\Librairies\Voltage\Api\manager\ScoreBoardManager;

class ScoreBoardApi extends PluginBase
{
    private static ?ScoreBoardManager $manager = null;

    /**
     * @return ScoreBoardManager|null
     */
    public static function getManager() : ?ScoreBoardManager {
        return self::$manager;
    }

    public static function loadManager(): void
    {
        self::$manager = new ScoreBoardManager();
    }
}