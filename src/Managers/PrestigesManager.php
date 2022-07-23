<?php

namespace Legacy\ThePit\Managers;

use JetBrains\PhpStorm\Pure;
use Legacy\ThePit\Core;
use Legacy\ThePit\Objects\Prestige;
use Legacy\ThePit\Utils\PrestigesUtils;

abstract class PrestigesManager
{
    /**
     * @var Prestige[]
     */
    private static array $levels = [];

    /**
     * @return Prestige[]
     */
    #[Pure] public static function getLevels(): array {
        return [
            new Prestige(PrestigesUtils::PRESTIGE_LEVELS_REACH_1, PrestigesUtils::PRESTIGE_LEVEL_1, PrestigesUtils::PRESTIGE_1),
        ];
    }

    public static function initPrestiges(): void {
        foreach (self::getLevels() as $level){
            self::$levels[$level->getName()] = $level;
            Core::getInstance()->getLogger()->notice("[PRESTIGES] Loaded");
        }
    }

    public static function getLevel(string $name): Prestige {
        return self::$levels[$name] ?? reset(self::$levels);
    }
}
