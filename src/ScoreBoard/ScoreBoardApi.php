<?php

namespace Legacy\ThePit\ScoreBoard;

use pocketmine\plugin\PluginBase;
use Legacy\ThePit\ScoreBoard\manager\ScoreBoardManager;

abstract class ScoreBoardApi
{
    private static ?ScoreBoardManager $manager = null;

    /**
     * @return ScoreBoardManager|null
     */
    public static function getManager(): ?ScoreBoardManager
    {
        return self::$manager;
    }

    public static function loadManager(): void
    {
        self::$manager = new ScoreBoardManager();
    }
}