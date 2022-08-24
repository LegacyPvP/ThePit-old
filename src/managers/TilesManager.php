<?php

namespace Legacy\ThePit\managers;

use Legacy\ThePit\Core;
use Legacy\ThePit\tiles\list\CrateTile;
use pocketmine\block\tile\TileFactory;

final class TilesManager extends Managers
{
    public function getAll(): array
    {
        return [
            [CrateTile::class, ["Crate", "minecraft:crate"]],
        ];
    }

    public function init(): void
    {
        foreach (self::getAll() as [$class, $names]) {
            TileFactory::getInstance()->register($class, $names);
            Core::getInstance()->getLogger()->notice("[TILES] Tile: $names[0] Loaded");
        }
    }
}